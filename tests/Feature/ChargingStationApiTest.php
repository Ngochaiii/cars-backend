<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ChargingStationApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.open_charge_map.key' => 'ocm-test-key',
            'services.open_charge_map.url' => 'https://api.openchargemap.io/v3',
            'services.open_charge_map.country_code' => 'VN',
            'services.open_charge_map.distance_km' => 80,
            'services.open_charge_map.max_results' => 12,
            'services.open_charge_map.cache_seconds' => 21600,
            'services.overpass.url' => 'https://overpass-api.de/api/interpreter',
            'services.overpass.distance_km' => 80,
            'services.overpass.max_results' => 12,
            'services.overpass.cache_seconds' => 21600,
            'services.overpass.timeout' => 25,
            'services.overpass.query_timeout' => 20,
            'services.nominatim.url' => 'https://nominatim.openstreetmap.org/search',
            'services.nominatim.user_agent' => 'Cars tests (https://example.test)',
            'services.nominatim.cache_seconds' => 2592000,
            'services.photon.url' => 'https://photon.komoot.io/api/',
            'services.photon.timeout' => 10,
        ]);

        RateLimiter::clear('nominatim-public-global');
    }

    public function test_tim_quanh_toa_do_tra_dung_hop_dong_json_cho_giao_dien(): void
    {
        Http::fake([
            'api.openchargemap.io/*' => Http::response([$this->station()]),
        ]);

        $this->getJson('/api/v1/charging-stations?lat=21.2800&lng=106.2000')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'V-GREEN Vincom Bắc Giang')
            ->assertJsonPath('data.0.status', 'Hoạt động')
            ->assertJsonPath('data.0.tone', 'ok')
            ->assertJsonPath('data.0.info', '4 điểm sạc · tối đa 60 kW · CCS (Type 2)')
            ->assertJsonPath('data.0.address', 'Đường Hùng Vương, Bắc Giang')
            ->assertJsonPath('data.0.distance', 0)
            ->assertJsonPath('meta.source', 'Open Charge Map')
            ->assertJsonPath('meta.realtime_availability', false)
            ->assertJsonMissing(['key' => 'ocm-test-key']);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();

            return str_starts_with($request->url(), 'https://api.openchargemap.io/v3/poi/')
                && $data['key'] === 'ocm-test-key'
                && $data['countrycode'] === 'VN'
                && (float) $data['latitude'] === 21.28
                && (float) $data['longitude'] === 106.2
                && $data['distanceunit'] === 'KM';
        });
    }

    public function test_nhap_dia_chi_chi_goi_nominatim_mot_lan_roi_tim_tram(): void
    {
        $query = 'Phường Ngô Quyền Bắc Giang '.bin2hex(random_bytes(3));

        Http::fake(function (Request $request) {
            if (str_starts_with($request->url(), 'https://nominatim.openstreetmap.org/search')) {
                return Http::response([['lat' => '21.2800', 'lon' => '106.2000']]);
            }

            return Http::response([$this->station()]);
        });

        $this->getJson('/api/v1/charging-stations?q='.urlencode($query))
            ->assertOk()
            ->assertJsonPath('data.0.name', 'V-GREEN Vincom Bắc Giang')
            ->assertJsonPath('meta.latitude', 21.28)
            ->assertJsonPath('meta.longitude', 106.2);

        Http::assertSentCount(2);
        Http::assertSent(function (Request $request): bool {
            return str_starts_with($request->url(), 'https://nominatim.openstreetmap.org/search')
                && $request->hasHeader('User-Agent', 'Cars tests (https://example.test)')
                && data_get($request->data(), 'countrycodes') === 'vn'
                && data_get($request->data(), 'limit') === 1;
        });
    }

    public function test_cung_khu_vuc_duoc_cache_de_khong_ton_request_mien_phi(): void
    {
        $lat = 21.31 + random_int(1, 50) / 10000;
        $lng = 106.21 + random_int(1, 50) / 10000;

        Http::fake([
            'api.openchargemap.io/*' => Http::response([$this->station()]),
        ]);

        $url = "/api/v1/charging-stations?lat={$lat}&lng={$lng}";

        $this->getJson($url)->assertOk();
        $this->getJson($url)->assertOk();

        Http::assertSentCount(1);
    }

    public function test_nominatim_loi_thi_tu_dung_photon_khong_can_key(): void
    {
        config(['services.open_charge_map.key' => null]);
        $query = 'Thành phố Bắc Giang '.bin2hex(random_bytes(3));

        Http::fake(function (Request $request) {
            if (str_starts_with($request->url(), 'https://nominatim.openstreetmap.org/search')) {
                return Http::response(['message' => 'temporary error'], 503);
            }

            if (str_starts_with($request->url(), 'https://photon.komoot.io/api/')) {
                return Http::response([
                    'type' => 'FeatureCollection',
                    'features' => [[
                        'type' => 'Feature',
                        'geometry' => ['type' => 'Point', 'coordinates' => [106.20, 21.28]],
                        'properties' => [
                            'name' => 'Thành phố Bắc Giang',
                            'country' => 'Việt Nam',
                            'countrycode' => 'VN',
                        ],
                    ]],
                ]);
            }

            return Http::response(['elements' => [$this->openStreetMapStation()]]);
        });

        $this->getJson('/api/v1/charging-stations?q='.urlencode($query))
            ->assertOk()
            ->assertJsonPath('meta.source', 'OpenStreetMap')
            ->assertJsonPath('meta.latitude', 21.28)
            ->assertJsonPath('meta.longitude', 106.2)
            ->assertJsonPath('data.0.name', 'V-GREEN Big C Bắc Giang');

        Http::assertSentCount(3);
        Http::assertSent(function (Request $request) use ($query): bool {
            return str_starts_with($request->url(), 'https://photon.komoot.io/api/')
                && data_get($request->data(), 'q') === $query.', Việt Nam'
                && data_get($request->data(), 'limit') === 5;
        });
    }

    public function test_khong_co_key_thi_tu_dung_openstreetmap_mien_phi(): void
    {
        config(['services.open_charge_map.key' => null]);
        Http::fake([
            'overpass-api.de/*' => Http::response([
                'elements' => [$this->openStreetMapStation()],
            ]),
        ]);

        $url = '/api/v1/charging-stations?lat=21.28&lng=106.20';

        $this->getJson($url)
            ->assertOk()
            ->assertJsonPath('data.0.name', 'V-GREEN Big C Bắc Giang')
            ->assertJsonPath('data.0.status', 'Thông tin tham khảo')
            ->assertJsonPath('data.0.info', '6 điểm sạc · tối đa 60 kW · CCS2, Type 2 · Mở 24/7')
            ->assertJsonPath('data.0.address', 'Hùng Vương, Bắc Giang')
            ->assertJsonPath('meta.source', 'OpenStreetMap')
            ->assertJsonPath('meta.attribution_url', 'https://www.openstreetmap.org/copyright')
            ->assertJsonPath('meta.realtime_availability', false);

        // Cùng một khu vực phải đọc cache, không gọi lại API công cộng.
        $this->getJson($url)->assertOk();

        Http::assertSentCount(1);

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && $request->url() === 'https://overpass-api.de/api/interpreter'
                && $request->hasHeader('User-Agent', 'Cars tests (https://example.test)')
                && str_contains((string) data_get($request->data(), 'data'), '["amenity"="charging_station"]')
                && str_contains((string) data_get($request->data(), 'data'), 'around:80000,21.280000,106.200000');
        });
    }

    public function test_openstreetmap_loi_thi_tra_503_de_frontend_dung_danh_sach_du_phong(): void
    {
        config(['services.open_charge_map.key' => null]);
        Http::fake([
            'overpass-api.de/*' => Http::response('busy', 503),
        ]);

        $this->getJson('/api/v1/charging-stations?lat=21.29&lng=106.21')
            ->assertServiceUnavailable()
            ->assertJsonPath('data', [])
            ->assertJsonPath('message', 'Chưa lấy được dữ liệu trạm sạc. Website đang hiển thị danh sách dự phòng.');
    }

    public function test_thieu_vi_tri_thi_bi_chan_truoc_khi_goi_api(): void
    {
        Http::fake();

        $this->getJson('/api/v1/charging-stations')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('q');

        Http::assertNothingSent();
    }

    /** @return array<string, mixed> */
    private function station(): array
    {
        return [
            'ID' => 12345,
            'AddressInfo' => [
                'Title' => 'V-GREEN Vincom Bắc Giang',
                'AddressLine1' => 'Đường Hùng Vương',
                'Town' => 'Bắc Giang',
                'StateOrProvince' => 'Bắc Giang',
                'Latitude' => 21.28,
                'Longitude' => 106.20,
            ],
            'OperatorInfo' => ['Title' => 'V-GREEN'],
            'StatusType' => ['IsOperational' => true],
            'NumberOfPoints' => 4,
            'Connections' => [[
                'PowerKW' => 60,
                'ConnectionType' => ['Title' => 'CCS (Type 2)'],
            ]],
        ];
    }

    /** @return array<string, mixed> */
    private function openStreetMapStation(): array
    {
        return [
            'type' => 'node',
            'id' => 998877,
            'lat' => 21.28,
            'lon' => 106.20,
            'tags' => [
                'amenity' => 'charging_station',
                'name' => 'V-GREEN Big C Bắc Giang',
                'operator' => 'V-GREEN',
                'capacity' => '6',
                'socket:type2_combo' => '4',
                'socket:type2' => '2',
                'socket:type2_combo:output' => '60 kW',
                'opening_hours' => '24/7',
                'addr:street' => 'Hùng Vương',
                'addr:city' => 'Bắc Giang',
            ],
        ];
    }
}
