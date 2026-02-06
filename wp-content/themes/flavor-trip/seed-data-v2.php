<?php
/**
 * TripTalk 시드 데이터 v2 - 가치 중심 콘텐츠
 *
 * 실행 방법:
 * wp eval-file /var/www/html/wp-content/themes/flavor-trip/seed-data-v2.php --allow-root
 *
 * @package TripTalk
 */

if (!defined('WP_CLI') || !WP_CLI) {
    echo "이 스크립트는 WP-CLI에서만 실행할 수 있습니다.\n";
    exit(1);
}

WP_CLI::log('=== TripTalk 가치 중심 콘텐츠 생성 시작 ===');

// ─────────────────────────────────────────────
// 1. 기존 travel_itinerary 포스트 삭제
// ─────────────────────────────────────────────
WP_CLI::log('');
WP_CLI::log('── 1단계: 기존 여행 일정 삭제 ──');

$existing = get_posts([
    'post_type'      => 'travel_itinerary',
    'posts_per_page' => -1,
    'post_status'    => 'any',
]);

foreach ($existing as $post) {
    wp_delete_post($post->ID, true);
    WP_CLI::log("  [-] #{$post->ID} {$post->post_title} 삭제");
}

// ─────────────────────────────────────────────
// 2. Destination/Style 택소노미 ID 가져오기
// ─────────────────────────────────────────────
function get_term_id_by_slug($slug, $taxonomy) {
    $term = get_term_by('slug', $slug, $taxonomy);
    return $term ? $term->term_id : null;
}

// ─────────────────────────────────────────────
// 3. 새로운 가치 중심 콘텐츠
// ─────────────────────────────────────────────
WP_CLI::log('');
WP_CLI::log('── 2단계: 가치 중심 여행 일정 생성 ──');

$itineraries = [
    // ═══════════════════════════════════════════
    // 제주도 - 아이와 함께
    // ═══════════════════════════════════════════
    [
        'title'       => '아이와 함께할 때 동선을 40% 줄여주는 제주 서쪽 코스',
        'excerpt'     => '5세 이하 아이와 제주 여행? 서쪽 해안을 따라 이동 시간은 줄이고, 아이가 즐길 수 있는 명소만 엄선했습니다. 실제 부모들의 후기를 반영한 2박3일 코스.',
        'content'     => '<p>제주도 가족여행에서 가장 힘든 건 <strong>이동 시간</strong>입니다. 아이가 차에서 지치면 여행 전체가 힘들어지죠.</p>
<p>이 코스는 제주 서쪽 해안을 따라 <strong>모든 명소가 30분 이내</strong> 거리에 있도록 설계했습니다. 일반적인 제주 일주 코스 대비 <strong>이동 시간 40% 절감</strong>됩니다.</p>
<h3>이 코스의 핵심 포인트</h3>
<ul>
<li>모든 명소 간 이동시간 30분 이내</li>
<li>유아 동반 시 필수인 화장실/수유실 정보 포함</li>
<li>아이 메뉴가 있는 식당만 선별</li>
<li>낮잠 시간을 고려한 오후 2-4시 이동 배치</li>
</ul>',
        'dest_slugs'  => ['jeju', 'korea'],
        'styles'      => ['가족여행'],
        'meta'        => [
            '_ft_destination_name' => '제주도',
            '_ft_duration'         => '2박3일',
            '_ft_price_range'      => 'moderate',
            '_ft_difficulty'       => 'easy',
            '_ft_best_season'      => '4월~6월, 9월~10월',
            '_ft_highlights'       => '이동시간 40% 절감, 아이 친화 명소, 유아식 맛집',
        ],
        'days' => [
            [
                'title'   => '제주 서쪽 해안 시작',
                'summary' => '공항에서 30분 거리, 서쪽 해안의 아이 친화 명소 집중 공략',
                'tip'     => '렌터카는 카시트 포함 예약 필수. 공항 근처보다 애월 숙소가 다음날 이동에 유리합니다.',
                'spots'   => [
                    [
                        'type'        => 'place',
                        'time'        => '10:00',
                        'name'        => '협재해수욕장',
                        'description' => '수심이 얕아 5세 이하도 안전하게 물놀이 가능. 투명한 에메랄드빛 바다.',
                        'duration'    => '1시간 30분',
                        'tip'         => '해변 바로 앞 공영주차장 무료. 샤워시설 완비.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '12:00',
                        'name'        => '협재해물칼국수',
                        'cuisine'     => '한식',
                        'description' => '해물칼국수가 시그니처. 아이용 잔치국수(5,000원) 별도 메뉴 있음.',
                        'menu'        => '해물칼국수, 아이용 잔치국수',
                        'price'       => '1인 10,000원',
                        'wait_tip'    => '11:30 전 도착 시 웨이팅 없음',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '14:00',
                        'name'        => '오설록 티뮤지엄',
                        'description' => '녹차밭 산책로가 유모차 접근 가능. 아이스크림으로 아이 달래기 좋음.',
                        'duration'    => '1시간',
                        'tip'         => '녹차 아이스크림 필수. 오후 2-3시가 가장 한적.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '16:00',
                        'name'        => '새별오름',
                        'description' => '왕복 30분 가벼운 산책. 정상에서 보는 일몰이 환상적.',
                        'duration'    => '40분',
                        'tip'         => '유모차 불가. 아기띠 준비하세요. 일몰 1시간 전 도착 권장.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '18:30',
                        'name'        => '애월 해녀의집',
                        'cuisine'     => '해산물',
                        'description' => '싱싱한 회와 해산물 뚝배기. 아이용 계란찜 서비스.',
                        'menu'        => '모듬회, 전복죽',
                        'price'       => '2인 60,000원',
                        'wait_tip'    => '전화 예약 필수 (064-799-4442)',
                    ],
                ],
            ],
            [
                'title'   => '자연 속 체험 + 휴식',
                'summary' => '오전에 체험, 오후 낮잠 시간에 이동, 저녁 여유롭게',
                'tip'     => '낮 12시-2시 사이에 숙소 체크인하고 아이 낮잠 재우세요. 컨디션 관리가 여행의 핵심.',
                'spots'   => [
                    [
                        'type'        => 'place',
                        'time'        => '09:30',
                        'name'        => '에코랜드',
                        'description' => '숲속 기차 타고 곶자왈 탐험. 아이들이 가장 좋아하는 제주 명소 1위.',
                        'duration'    => '2시간',
                        'tip'         => '기차 첫 회차(09:30) 탑승하면 대기 없음. 마지막 역에서 피크닉 가능.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '12:00',
                        'name'        => '우진해장국',
                        'cuisine'     => '한식',
                        'description' => '제주 현지인 맛집. 고사리육개장이 시그니처. 반찬으로 나오는 멜젓이 별미.',
                        'menu'        => '고사리육개장, 몸국',
                        'price'       => '1인 10,000원',
                        'wait_tip'    => '제주시 본점 말고 조천점이 덜 붐빔',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '15:00',
                        'name'        => '함덕해수욕장',
                        'description' => '수심이 얕고 모래가 고와 아이 물놀이 최적. 해변 카페 즐비.',
                        'duration'    => '1시간 30분',
                        'tip'         => '썰물 때 더 넓은 모래사장. 조수 시간 미리 확인.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '18:00',
                        'name'        => '돈사돈 제주본점',
                        'cuisine'     => '흑돼지',
                        'description' => '제주 흑돼지 맛집. 좌식/테이블석 선택 가능.',
                        'menu'        => '흑돼지 근고기, 오겹살',
                        'price'       => '2인 50,000원',
                        'wait_tip'    => '오후 5시 전 방문 시 바로 입장',
                    ],
                ],
            ],
            [
                'title'   => '공항 방면 마무리',
                'summary' => '귀국일 아침, 공항 방향으로 이동하며 마지막 명소',
                'tip'     => '비행기 3시간 전까지 공항 도착 목표. 렌터카 반납 시간 30분 여유 두세요.',
                'spots'   => [
                    [
                        'type'        => 'place',
                        'time'        => '09:00',
                        'name'        => '도두봉',
                        'description' => '공항에서 10분. 20분이면 정상. 비행기 이착륙 보며 아이와 마무리.',
                        'duration'    => '40분',
                        'tip'         => '무지개해안도로와 연결. 드라이브 코스로도 좋음.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '10:30',
                        'name'        => '삼대국수회관',
                        'cuisine'     => '한식',
                        'description' => '제주식 고기국수 원조. 담백한 국물이 아이도 잘 먹음.',
                        'menu'        => '고기국수',
                        'price'       => '1인 9,000원',
                        'wait_tip'    => '공항 근처라 항상 붐빔. 10시 오픈 맞춰 가세요.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '11:30',
                        'name'        => '렌터카 반납 및 공항 이동',
                        'description' => '제주공항 근처 렌터카 반납 후 셔틀버스로 공항 이동.',
                        'duration'    => '30분',
                        'tip'         => '주유는 반납 전 공항 근처 주유소에서. 대기줄 짧음.',
                    ],
                ],
            ],
        ],
    ],

    // ═══════════════════════════════════════════
    // 도쿄 디즈니랜드
    // ═══════════════════════════════════════════
    [
        'title'       => '대기 시간 총 3시간 절약하는 도쿄 디즈니랜드 동선 설계도',
        'excerpt'     => '디즈니랜드에서 줄 서느라 하루를 낭비하지 마세요. 프리미어 액세스 + 최적 동선으로 인기 어트랙션 8개를 웨이팅 최소화로 즐기는 완벽 공략법.',
        'content'     => '<p>도쿄 디즈니랜드 평균 대기시간은 인기 어트랙션 기준 <strong>60-90분</strong>입니다. 하루에 4-5개 타면 대기만 4시간...</p>
<p>이 가이드는 <strong>프리미어 액세스(유료 패스트패스)</strong>와 <strong>오픈런 동선</strong>을 조합해 대기시간을 <strong>총 3시간 이상 절약</strong>합니다.</p>
<h3>핵심 전략</h3>
<ul>
<li>오픈 15분 전 도착 → 입장 직후 동선이 승부처</li>
<li>프리미어 액세스는 미녀와 야수, 베이맥스에 사용</li>
<li>스탠바이 패스가 필요한 어트랙션 우선 확보</li>
<li>퍼레이드 시간에 인기 어트랙션 공략</li>
</ul>',
        'dest_slugs'  => ['tokyo', 'japan'],
        'styles'      => ['가족여행', '문화체험'],
        'meta'        => [
            '_ft_destination_name' => '도쿄',
            '_ft_duration'         => '1일',
            '_ft_price_range'      => 'moderate',
            '_ft_difficulty'       => 'moderate',
            '_ft_best_season'      => '3월~5월, 10월~11월',
            '_ft_highlights'       => '대기시간 3시간 절약, 인기 어트랙션 8개, 프리미어 액세스 활용법',
        ],
        'days' => [
            [
                'title'   => '디즈니랜드 완벽 공략',
                'summary' => '오픈런부터 불꽃놀이까지, 분단위로 설계된 동선',
                'tip'     => '디즈니 앱 필수 설치. 프리미어 액세스는 당일 앱에서 구매 가능 (1,500-2,000엔/1회).',
                'spots'   => [
                    [
                        'type'        => 'place',
                        'time'        => '07:45',
                        'name'        => '마이하마역 도착',
                        'description' => '오픈 8시 기준, 15분 전 게이트 앞 대기. 이미 줄이 있어도 당황 금지.',
                        'tip'         => 'JR 게이요선 마이하마역에서 도보 5분. 스이카 잔액 미리 확인.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '08:00',
                        'name'        => '입장 직후 → 빅썬더 마운틴',
                        'description' => '입장하자마자 웨스턴랜드로 직행. 오픈런 시 대기 15분 이내.',
                        'duration'    => '30분',
                        'tip'         => '달리지 마세요! 빠른 걸음으로. 뛰면 캐스트에게 제지당함.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '08:45',
                        'name'        => '스플래시 마운틴',
                        'description' => '빅썬더 마운틴 바로 옆. 연달아 탑승하면 효율 극대화.',
                        'duration'    => '40분',
                        'tip'         => '앞좌석은 덜 젖음. 우비 준비하면 더 좋음.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '09:30',
                        'name'        => '앱에서 프리미어 액세스 구매',
                        'description' => '미녀와 야수 "마법의 이야기" 프리미어 액세스 구매. 시간대는 오후 선택.',
                        'tip'         => '인기 시간대는 금방 마감. 구매 가능해지면 바로 결제.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '10:30',
                        'name'        => '그랑마 사라의 키친',
                        'cuisine'     => '양식',
                        'description' => '크리터컨트리 내 레스토랑. 일찍 점심 먹고 오후 피크 피하기.',
                        'menu'        => '그랑마 사라의 스페셜 세트',
                        'price'       => '1,800엔',
                        'wait_tip'    => '11시 전이면 바로 입장',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '11:30',
                        'name'        => '캐리비안의 해적',
                        'description' => '점심시간대는 야외 어트랙션으로 사람 몰림. 실내는 비교적 한산.',
                        'duration'    => '30분',
                        'tip'         => '잭 스패로우 등장 장면 놓치지 마세요.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '12:30',
                        'name'        => '호라이티드 맨션',
                        'description' => '실내 어트랙션 연속 공략. 대기 25분 이내면 탑승.',
                        'duration'    => '25분',
                        'tip'         => '무덤 비석의 숨은 메시지 찾아보기.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '13:30',
                        'name'        => '드리밍 업! 퍼레이드 감상',
                        'description' => '퍼레이드 시작 30분 전부터 자리 잡기. 이 시간 인기 어트랙션 대기 감소.',
                        'duration'    => '45분',
                        'tip'         => '퍼레이드 경로 끝자락(투모로우랜드 방면)이 덜 붐빔.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '15:00',
                        'name'        => '미녀와 야수 (프리미어 액세스)',
                        'description' => '구매해둔 프리미어 액세스로 대기 없이 입장.',
                        'duration'    => '30분',
                        'tip'         => '예약 시간 5분 전 도착. 늦으면 무효 처리됨.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '16:00',
                        'name'        => '베이맥스의 해피라이드',
                        'description' => '투모로우랜드 신규 어트랙션. 평소 90분 대기, 저녁 시간대는 60분.',
                        'duration'    => '45분',
                        'tip'         => '싱글라이더 이용하면 대기 절반으로 단축.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '17:30',
                        'name'        => '이스트사이드 카페',
                        'cuisine'     => '양식',
                        'description' => '월드바자 내 레스토랑. 불꽃놀이 전 여유있게 저녁.',
                        'menu'        => '스페셜 코스',
                        'price'       => '2,500엔',
                        'wait_tip'    => '예약 없이 방문 가능. 창가석 요청하세요.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '19:00',
                        'name'        => '스페이스 마운틴',
                        'description' => '저녁 식사 시간대 대기 감소. 40분 이내면 탑승.',
                        'duration'    => '35분',
                        'tip'         => '어두운 실내 롤러코스터. 무서우면 앞좌석 선택.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '20:30',
                        'name'        => '불꽃놀이 & 캐슬 프로젝션',
                        'description' => '신데렐라 성 앞 최고의 위치에서 하루 마무리.',
                        'duration'    => '20분',
                        'tip'         => '플라자 중앙보다 살짝 오른쪽이 사진 찍기 좋음.',
                    ],
                ],
            ],
        ],
    ],

    // ═══════════════════════════════════════════
    // 오사카 도톤보리
    // ═══════════════════════════════════════════
    [
        'title'       => '웨이팅 없이 즐기는 도톤보리 맛집 5곳의 틈새 방문 시간',
        'excerpt'     => '도톤보리 인기 맛집은 평균 대기 40분. 하지만 "틈새 시간"을 노리면 줄 없이 들어갈 수 있습니다. 현지인 팁 기반 완벽 타이밍 가이드.',
        'content'     => '<p>도톤보리는 오사카 여행의 하이라이트지만, 인기 맛집은 <strong>점심 12-14시, 저녁 18-20시</strong>에 긴 줄이 생깁니다.</p>
<p>이 가이드는 각 맛집의 <strong>"틈새 방문 시간"</strong>을 알려드립니다. 현지인들이 실제로 이용하는 시간대예요.</p>
<h3>틈새 시간의 비밀</h3>
<ul>
<li>오픈 직후 15분 - 첫 번째 웨이브 전</li>
<li>오후 3-5시 - 점심/저녁 사이 공백</li>
<li>밤 9시 이후 - 단체 관광객 빠진 후</li>
</ul>',
        'dest_slugs'  => ['osaka', 'japan'],
        'styles'      => ['맛집투어', '가성비여행'],
        'meta'        => [
            '_ft_destination_name' => '오사카',
            '_ft_duration'         => '1일',
            '_ft_price_range'      => 'budget',
            '_ft_difficulty'       => 'easy',
            '_ft_best_season'      => '3월~5월, 10월~11월',
            '_ft_highlights'       => '웨이팅 제로, 도톤보리 맛집 5곳, 틈새 시간 공략',
        ],
        'days' => [
            [
                'title'   => '도톤보리 맛집 완전 정복',
                'summary' => '아침부터 밤까지, 웨이팅 없는 맛집 투어 동선',
                'tip'     => '도톤보리는 난바역에서 도보 5분. 이 코스는 모두 걸어서 이동 가능한 거리입니다.',
                'spots'   => [
                    [
                        'type'        => 'place',
                        'time'        => '09:30',
                        'name'        => '구로몬 시장',
                        'description' => '도톤보리 맛집 투어 전 워밍업. 신선한 해산물과 과일로 아침 해결.',
                        'duration'    => '1시간',
                        'tip'         => '마구로(참치)와 딸기는 꼭 맛보세요. 일요일은 휴무 가게 많음.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '11:00',
                        'name'        => '이치란 라멘 도톤보리점',
                        'cuisine'     => '라멘',
                        'description' => '돈코츠 라멘 명가. 1인 칸막이석이 특징. 맛 커스터마이징 가능.',
                        'menu'        => '천연 돈코츠 라멘 + 반숙란',
                        'price'       => '1,090엔',
                        'wait_tip'    => '오픈(11시) 직후 도착하면 대기 0분. 정오 넘으면 30분+',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '12:30',
                        'name'        => '도톤보리 글리코 사인',
                        'description' => '점심 먹고 산책하며 인증샷. 글리코 러닝맨 앞이 포토존.',
                        'duration'    => '30분',
                        'tip'         => '다리 위에서 찍으면 글리코 사인이 정면으로 나옴.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '15:00',
                        'name'        => '타코야키 와나카 본점',
                        'cuisine'     => '타코야키',
                        'description' => '도톤보리 타코야키 원조. 겉바속촉 타코야키 12개 세트.',
                        'menu'        => '타코야키 12개 (소스/폰즈/소금 3종)',
                        'price'       => '700엔',
                        'wait_tip'    => '오후 3-5시가 황금 시간. 저녁 시간대는 40분 대기.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '16:00',
                        'name'        => '치보 오코노미야키',
                        'cuisine'     => '오코노미야키',
                        'description' => '오사카 소울푸드 오코노미야키. 직접 구워먹는 재미.',
                        'menu'        => '치보 스페셜 (돼지고기+새우+오징어)',
                        'price'       => '1,650엔',
                        'wait_tip'    => '16시 방문이 베스트. 저녁 시간대 피크 전 여유롭게.',
                    ],
                    [
                        'type'        => 'place',
                        'time'        => '17:30',
                        'name'        => '돈키호테 도톤보리점',
                        'description' => '기념품 쇼핑. 과자, 화장품, 잡화 뭐든 있음. 면세 가능.',
                        'duration'    => '1시간',
                        'tip'         => '관람차(돈키호테 위)에서 도톤보리 야경 감상 가능.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '21:00',
                        'name'        => '킨류 라멘',
                        'cuisine'     => '라멘',
                        'description' => '24시간 영업 라멘집. 황금 용 간판이 랜드마크.',
                        'menu'        => '킨류 라멘 + 교자',
                        'price'       => '1,000엔',
                        'wait_tip'    => '밤 9시 이후 단체 관광객 빠지고 한산해짐.',
                    ],
                    [
                        'type'        => 'restaurant',
                        'time'        => '22:00',
                        'name'        => '쿠시카츠 다루마',
                        'cuisine'     => '쿠시카츠',
                        'description' => '오사카 쿠시카츠(꼬치튀김) 명가. "소스 2번 찍기 금지" 규칙 유명.',
                        'menu'        => '쿠시카츠 세트 10종',
                        'price'       => '1,500엔',
                        'wait_tip'    => '밤 10시 이후가 틈새 시간. 자정까지 영업.',
                    ],
                ],
            ],
        ],
    ],
];

// ─────────────────────────────────────────────
// 4. 포스트 생성
// ─────────────────────────────────────────────
foreach ($itineraries as $itinerary) {
    $post_id = wp_insert_post([
        'post_title'   => $itinerary['title'],
        'post_content' => $itinerary['content'],
        'post_excerpt' => $itinerary['excerpt'],
        'post_status'  => 'publish',
        'post_type'    => 'travel_itinerary',
        'post_author'  => 1,
    ]);

    if (is_wp_error($post_id)) {
        WP_CLI::warning("  일정 '{$itinerary['title']}' 생성 실패: " . $post_id->get_error_message());
        continue;
    }

    // 메타 필드
    foreach ($itinerary['meta'] as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    // 일자별 일정
    update_post_meta($post_id, '_ft_days', $itinerary['days']);

    // Destination 택소노미
    $dest_ids = [];
    foreach ($itinerary['dest_slugs'] as $slug) {
        $term_id = get_term_id_by_slug($slug, 'destination');
        if ($term_id) $dest_ids[] = $term_id;
    }
    if (!empty($dest_ids)) {
        wp_set_object_terms($post_id, $dest_ids, 'destination');
    }

    // Travel Style 택소노미
    $style_ids = [];
    foreach ($itinerary['styles'] as $name) {
        $term = get_term_by('name', $name, 'travel_style');
        if ($term) $style_ids[] = $term->term_id;
    }
    if (!empty($style_ids)) {
        wp_set_object_terms($post_id, $style_ids, 'travel_style');
    }

    WP_CLI::log("  [+] #{$post_id} {$itinerary['title']}");
}

WP_CLI::log('');
WP_CLI::success('가치 중심 콘텐츠 생성 완료!');
WP_CLI::log('');
WP_CLI::log('생성된 콘텐츠:');
WP_CLI::log('  - 제주도: 아이와 함께할 때 동선을 40% 줄여주는 제주 서쪽 코스');
WP_CLI::log('  - 도쿄: 대기 시간 총 3시간 절약하는 도쿄 디즈니랜드 동선 설계도');
WP_CLI::log('  - 오사카: 웨이팅 없이 즐기는 도톤보리 맛집 5곳의 틈새 방문 시간');
WP_CLI::log('');
WP_CLI::log('검증: wp post list --post_type=travel_itinerary --allow-root');
