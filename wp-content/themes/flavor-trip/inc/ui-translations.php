<?php
/**
 * 런타임 UI 번역 시스템
 *
 * .mo 파일 없이 Polylang 현재 언어에 맞춰 __() / esc_html_e() 출력을 자동 번역.
 * gettext 필터를 사용하므로 기존 템플릿 수정 불필요.
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

/**
 * 모든 UI 문자열 번역 테이블
 * 키: 한국어 원본 → 값: [lang_slug => 번역]
 */
function ft_get_ui_translations() {
    static $cache = null;
    if ($cache !== null) return $cache;

    $cache = [
        // ── 가격대 라벨 ──
        '가성비'   => ['en' => 'Budget',    'ja' => 'お手頃',        'zh-cn' => '经济实惠', 'zh-tw' => '經濟實惠', 'fr' => 'Économique',  'de' => 'Günstig'],
        '프리미엄' => ['en' => 'Premium',   'ja' => 'プレミアム',    'zh-cn' => '高端',     'zh-tw' => '高端',     'fr' => 'Premium',     'de' => 'Premium'],
        '럭셔리'   => ['en' => 'Luxury',    'ja' => 'ラグジュアリー','zh-cn' => '奢华',     'zh-tw' => '奢華',     'fr' => 'Luxe',        'de' => 'Luxus'],

        // ── 난이도 라벨 ──
        '쉬움'   => ['en' => 'Easy',     'ja' => '簡単',   'zh-cn' => '简单', 'zh-tw' => '簡單', 'fr' => 'Facile',    'de' => 'Einfach'],
        '보통'   => ['en' => 'Moderate', 'ja' => '普通',   'zh-cn' => '适中', 'zh-tw' => '適中', 'fr' => 'Modéré',    'de' => 'Mittel'],
        '어려움' => ['en' => 'Hard',     'ja' => '難しい', 'zh-cn' => '困难', 'zh-tw' => '困難', 'fr' => 'Difficile', 'de' => 'Schwer'],

        // ── 빵크럼 ──
        '홈'       => ['en' => 'Home',              'ja' => 'ホーム',       'zh-cn' => '首页',     'zh-tw' => '首頁',     'fr' => 'Accueil',      'de' => 'Startseite'],
        '여행 일정' => ['en' => 'Travel Itineraries', 'ja' => '旅行プラン',  'zh-cn' => '旅行行程', 'zh-tw' => '旅行行程', 'fr' => 'Itinéraires',  'de' => 'Reiserouten'],

        // ── 사이드바 ──
        '여행 정보' => ['en' => 'Travel Info',          'ja' => '旅行情報',           'zh-cn' => '旅行信息',   'zh-tw' => '旅行資訊',   'fr' => 'Infos voyage',          'de' => 'Reiseinfo'],
        '목적지'     => ['en' => 'Destination',          'ja' => '目的地',             'zh-cn' => '目的地',     'zh-tw' => '目的地',     'fr' => 'Destination',           'de' => 'Reiseziel'],
        '여행 기간' => ['en' => 'Duration',             'ja' => '旅行期間',           'zh-cn' => '旅行时间',   'zh-tw' => '旅行時間',   'fr' => 'Durée',                 'de' => 'Reisedauer'],
        '가격대'     => ['en' => 'Price Range',          'ja' => '価格帯',             'zh-cn' => '价格范围',   'zh-tw' => '價格範圍',   'fr' => 'Gamme de prix',         'de' => 'Preisklasse'],
        '난이도'     => ['en' => 'Difficulty',            'ja' => '難易度',             'zh-cn' => '难度',       'zh-tw' => '難度',       'fr' => 'Difficulté',            'de' => 'Schwierigkeit'],
        '추천 시기' => ['en' => 'Best Season',           'ja' => 'おすすめ時期',       'zh-cn' => '推荐季节',   'zh-tw' => '推薦季節',   'fr' => 'Meilleure saison',      'de' => 'Beste Reisezeit'],
        '하이라이트' => ['en' => 'Highlights',            'ja' => 'ハイライト',         'zh-cn' => '亮点',       'zh-tw' => '亮點',       'fr' => 'Points forts',          'de' => 'Highlights'],
        '공유하기'   => ['en' => 'Share',                 'ja' => 'シェア',             'zh-cn' => '分享',       'zh-tw' => '分享',       'fr' => 'Partager',              'de' => 'Teilen'],
        '관련 일정' => ['en' => 'Related Itineraries',   'ja' => '関連プラン',         'zh-cn' => '相关行程',   'zh-tw' => '相關行程',   'fr' => 'Itinéraires similaires','de' => 'Ähnliche Routen'],

        // ── 싱글 일정 ──
        '일자별 일정' => ['en' => 'Daily Itinerary',  'ja' => '日別プラン',     'zh-cn' => '每日行程', 'zh-tw' => '每日行程', 'fr' => 'Programme journalier', 'de' => 'Tagesplan'],
        '여행 스타일' => ['en' => 'Travel Style',      'ja' => '旅行スタイル',   'zh-cn' => '旅行风格', 'zh-tw' => '旅行風格', 'fr' => 'Style de voyage',      'de' => 'Reisestil'],

        // ── 일정 Day 템플릿 ──
        '추천:'             => ['en' => 'Recommended:', 'ja' => 'おすすめ:',         'zh-cn' => '推荐:',       'zh-tw' => '推薦:',       'fr' => 'Recommandé :', 'de' => 'Empfohlen:'],
        '예약하기 →'        => ['en' => 'Book Now →',   'ja' => '予約する →',        'zh-cn' => '立即预订 →',  'zh-tw' => '立即預訂 →',  'fr' => 'Réserver →',   'de' => 'Buchen →'],
        '자세히 보기 →'     => ['en' => 'Learn More →', 'ja' => '詳しく見る →',      'zh-cn' => '了解更多 →',  'zh-tw' => '了解更多 →',  'fr' => 'En savoir plus →', 'de' => 'Mehr erfahren →'],
        '이 날의 핵심 팁'   => ['en' => "Today's Key Tips", 'ja' => 'この日のポイント', 'zh-cn' => '今日重点提示', 'zh-tw' => '今日重點提示', 'fr' => 'Conseils du jour', 'de' => 'Tipps des Tages'],
        '주요 장소:'        => ['en' => 'Key Places:',  'ja' => '主要スポット:',     'zh-cn' => '主要景点:',   'zh-tw' => '主要景點:',   'fr' => 'Lieux clés :',  'de' => 'Wichtige Orte:'],
        '💡 팁:'            => ['en' => '💡 Tip:',      'ja' => '💡 ヒント:',        'zh-cn' => '💡 提示:',    'zh-tw' => '💡 提示:',    'fr' => '💡 Conseil :',  'de' => '💡 Tipp:'],

        // ── 아카이브 ──
        '어디로 떠나볼까요?' => [
            'en' => 'Where Will You Go?',
            'ja' => 'どこへ行きますか？',
            'zh-cn' => '想去哪里旅行？',
            'zh-tw' => '想去哪裡旅行？',
            'fr' => 'Où allez-vous ?',
            'de' => 'Wohin geht die Reise?',
        ],
        '전 세계 다양한 여행 코스를 탐색하고 나만의 완벽한 여행을 계획해보세요.' => [
            'en' => 'Explore diverse travel itineraries worldwide and plan your perfect trip.',
            'ja' => '世界各地の多様な旅行コースを探索し、あなただけの完璧な旅を計画しましょう。',
            'zh-cn' => '探索世界各地的多样旅行路线，规划您的完美之旅。',
            'zh-tw' => '探索世界各地的多樣旅行路線，規劃您的完美之旅。',
            'fr' => 'Explorez des itinéraires variés dans le monde entier et planifiez votre voyage parfait.',
            'de' => 'Entdecken Sie vielfältige Reiserouten weltweit und planen Sie Ihre perfekte Reise.',
        ],
        '도시, 국가 또는 여행 스타일 검색...' => [
            'en' => 'Search city, country or travel style...',
            'ja' => '都市、国、旅行スタイルで検索...',
            'zh-cn' => '搜索城市、国家或旅行风格...',
            'zh-tw' => '搜尋城市、國家或旅行風格...',
            'fr' => 'Rechercher ville, pays ou style...',
            'de' => 'Stadt, Land oder Reisestil suchen...',
        ],
        '검색'   => ['en' => 'Search',       'ja' => '検索',     'zh-cn' => '搜索',   'zh-tw' => '搜尋',   'fr' => 'Rechercher',  'de' => 'Suchen'],
        '여행지' => ['en' => 'Destinations',  'ja' => '旅行先',   'zh-cn' => '目的地', 'zh-tw' => '目的地', 'fr' => 'Destinations', 'de' => 'Reiseziele'],
        '스타일' => ['en' => 'Style',         'ja' => 'スタイル', 'zh-cn' => '风格',   'zh-tw' => '風格',   'fr' => 'Style',        'de' => 'Stil'],
        '전체'   => ['en' => 'All',           'ja' => 'すべて',   'zh-cn' => '全部',   'zh-tw' => '全部',   'fr' => 'Tout',         'de' => 'Alle'],

        // ── 여행지 그리드 ──
        '인기 여행지'  => ['en' => 'Popular Destinations', 'ja' => '人気の旅行先',     'zh-cn' => '热门目的地', 'zh-tw' => '熱門目的地', 'fr' => 'Destinations populaires', 'de' => 'Beliebte Reiseziele'],
        '%d개의 일정'  => ['en' => '%d itineraries',       'ja' => '%d件のプラン',      'zh-cn' => '%d个行程',   'zh-tw' => '%d個行程',   'fr' => '%d itinéraires',          'de' => '%d Reiserouten'],

        // ── 검색 ──
        '"%s" 검색 결과' => [
            'en' => 'Search results for "%s"',
            'ja' => '「%s」の検索結果',
            'zh-cn' => '"%s"的搜索结果',
            'zh-tw' => '「%s」的搜尋結果',
            'fr' => 'Résultats pour « %s »',
            'de' => 'Suchergebnisse für „%s"',
        ],

        // ── 읽기 시간 ──
        '%d분 읽기' => ['en' => '%d min read', 'ja' => '%d分で読める', 'zh-cn' => '%d分钟阅读', 'zh-tw' => '%d分鐘閱讀', 'fr' => '%d min de lecture', 'de' => '%d Min. Lesezeit'],

        // ── ARIA 라벨 ──
        '페이지 네비게이션' => ['en' => 'Page navigation',       'ja' => 'ページナビゲーション',     'zh-cn' => '页面导航',   'zh-tw' => '頁面導覽',   'fr' => 'Navigation de page',  'de' => 'Seitennavigation'],
        '빵크럼 네비게이션' => ['en' => 'Breadcrumb navigation', 'ja' => 'パンくずナビゲーション',   'zh-cn' => '面包屑导航', 'zh-tw' => '麵包屑導覽', 'fr' => "Fil d'Ariane",        'de' => 'Brotkrümelnavigation'],

        // ── 메인 메뉴 / 헤더 / 푸터 ──
        '메인 메뉴'  => ['en' => 'Main Menu',   'ja' => 'メインメニュー', 'zh-cn' => '主菜单',   'zh-tw' => '主選單',   'fr' => 'Menu principal', 'de' => 'Hauptmenü'],
        '푸터 메뉴'  => ['en' => 'Footer Menu', 'ja' => 'フッターメニュー', 'zh-cn' => '页脚菜单', 'zh-tw' => '頁腳選單', 'fr' => 'Menu pied de page', 'de' => 'Fußzeilenmenü'],
        '본문으로 건너뛰기' => ['en' => 'Skip to content', 'ja' => '本文へスキップ', 'zh-cn' => '跳到正文', 'zh-tw' => '跳至正文', 'fr' => 'Aller au contenu', 'de' => 'Zum Inhalt springen'],
        '메뉴 열기'  => ['en' => 'Open menu', 'ja' => 'メニューを開く', 'zh-cn' => '打开菜单', 'zh-tw' => '開啟選單', 'fr' => 'Ouvrir le menu', 'de' => 'Menü öffnen'],
        '메뉴'       => ['en' => 'Menu',      'ja' => 'メニュー',       'zh-cn' => '菜单',     'zh-tw' => '選單',     'fr' => 'Menu',            'de' => 'Menü'],
        'All rights reserved.' => ['en' => 'All rights reserved.', 'ja' => 'All rights reserved.', 'zh-cn' => '保留所有权利。', 'zh-tw' => '保留所有權利。', 'fr' => 'Tous droits réservés.', 'de' => 'Alle Rechte vorbehalten.'],

        // ── 홈페이지 ──
        '추천 여행 일정' => ['en' => 'Featured Itineraries', 'ja' => 'おすすめ旅行プラン', 'zh-cn' => '推荐旅行行程', 'zh-tw' => '推薦旅行行程', 'fr' => 'Itinéraires recommandés', 'de' => 'Empfohlene Reiserouten'],
        '엄선된 여행 코스를 만나보세요' => ['en' => 'Discover curated travel plans', 'ja' => '厳選された旅行コースをご覧ください', 'zh-cn' => '发现精选旅行路线', 'zh-tw' => '發現精選旅行路線', 'fr' => 'Découvrez nos itinéraires sélectionnés', 'de' => 'Entdecken Sie ausgewählte Reiserouten'],
        '아직 등록된 여행 일정이 없습니다.' => ['en' => 'No itineraries available yet.', 'ja' => 'まだ旅行プランがありません。', 'zh-cn' => '暂无旅行行程。', 'zh-tw' => '暫無旅行行程。', 'fr' => 'Aucun itinéraire disponible.', 'de' => 'Noch keine Reiserouten verfügbar.'],
        '모든 일정 보기 →' => ['en' => 'View All Itineraries →', 'ja' => 'すべてのプランを見る →', 'zh-cn' => '查看所有行程 →', 'zh-tw' => '查看所有行程 →', 'fr' => 'Voir tous les itinéraires →', 'de' => 'Alle Reiserouten ansehen →'],
        '여행 이야기' => ['en' => 'Travel Stories', 'ja' => '旅の物語', 'zh-cn' => '旅行故事', 'zh-tw' => '旅行故事', 'fr' => 'Récits de voyage', 'de' => 'Reisegeschichten'],
        '생생한 여행 후기와 팁을 공유합니다' => ['en' => 'Real travel reviews and tips', 'ja' => 'リアルな旅行レビューとヒント', 'zh-cn' => '分享真实旅行评价和攻略', 'zh-tw' => '分享真實旅行評價和攻略', 'fr' => 'Avis et conseils de voyageurs', 'de' => 'Echte Reiseberichte und Tipps'],
        '아직 등록된 글이 없습니다.' => ['en' => 'No posts available yet.', 'ja' => 'まだ投稿がありません。', 'zh-cn' => '暂无文章。', 'zh-tw' => '暫無文章。', 'fr' => 'Aucun article disponible.', 'de' => 'Noch keine Beiträge verfügbar.'],

        // ── 히어로 섹션 ──
        '나만의 여행 일정 만들기' => ['en' => 'Create Your Own Itinerary', 'ja' => '自分だけの旅行プランを作る', 'zh-cn' => '创建专属旅行行程', 'zh-tw' => '建立專屬旅行行程', 'fr' => 'Créez votre itinéraire', 'de' => 'Eigene Reiseroute erstellen'],

        // ── 결과 없음 ──
        '결과가 없습니다' => ['en' => 'No Results Found', 'ja' => '結果が見つかりません', 'zh-cn' => '未找到结果', 'zh-tw' => '未找到結果', 'fr' => 'Aucun résultat', 'de' => 'Keine Ergebnisse'],
        '검색어와 일치하는 결과가 없습니다. 다른 키워드로 다시 검색해보세요.' => [
            'en' => 'No results match your search. Try different keywords.',
            'ja' => '検索に一致する結果がありません。別のキーワードで再検索してください。',
            'zh-cn' => '没有匹配的搜索结果，请尝试其他关键词。',
            'zh-tw' => '沒有匹配的搜尋結果，請嘗試其他關鍵詞。',
            'fr' => 'Aucun résultat ne correspond à votre recherche. Essayez d\'autres mots-clés.',
            'de' => 'Keine Ergebnisse gefunden. Versuchen Sie andere Suchbegriffe.',
        ],
        '아직 게시된 콘텐츠가 없습니다.' => ['en' => 'No content available yet.', 'ja' => 'まだコンテンツがありません。', 'zh-cn' => '暂无内容。', 'zh-tw' => '暫無內容。', 'fr' => 'Aucun contenu disponible.', 'de' => 'Noch keine Inhalte verfügbar.'],
    ];

    return $cache;
}

/**
 * 현재 Polylang 언어 슬러그 반환
 */
function ft_get_current_lang() {
    static $lang = null;
    if ($lang !== null) return $lang;

    if (function_exists('pll_current_language')) {
        $lang = pll_current_language() ?: 'ko';
    } else {
        $lang = 'ko';
    }
    return $lang;
}

/**
 * gettext 필터: flavor-trip 도메인의 모든 __() 호출을 가로채서 번역
 */
add_filter('gettext', function ($translated, $text, $domain) {
    if ($domain !== 'flavor-trip') return $translated;

    $lang = ft_get_current_lang();
    if ($lang === 'ko') return $translated;

    $strings = ft_get_ui_translations();
    if (!isset($strings[$text])) return $translated;

    // en-au → en 폴백
    $lang_fallback = [
        'en-au' => 'en',
    ];
    $lookup = $lang_fallback[$lang] ?? $lang;

    return $strings[$text][$lookup] ?? $translated;
}, 10, 3);

/**
 * gettext_with_context 필터 (동일 로직)
 */
add_filter('gettext_with_context', function ($translated, $text, $context, $domain) {
    if ($domain !== 'flavor-trip') return $translated;

    $lang = ft_get_current_lang();
    if ($lang === 'ko') return $translated;

    $strings = ft_get_ui_translations();
    if (!isset($strings[$text])) return $translated;

    $lang_fallback = ['en-au' => 'en', 'zh-hk' => 'zh-tw'];
    $lookup = $lang_fallback[$lang] ?? $lang;

    return $strings[$text][$lookup] ?? $translated;
}, 10, 4);

/**
 * 히어로 섹션 커스터마이저 값 번역
 * get_theme_mod()로 가져오는 값은 gettext 필터에 걸리지 않으므로 별도 처리
 */
$ft_hero_translations = [
    '맛있는 여행의 시작' => [
        'en' => 'Your Delicious Journey Starts Here',
        'ja' => 'おいしい旅のはじまり',
        'zh-cn' => '美味旅行从这里开始',
        'zh-tw' => '美味旅行從這裡開始',
        'fr' => 'Le début d\'un voyage savoureux',
        'de' => 'Der Beginn einer köstlichen Reise',
    ],
    '특별한 여행 일정을 만나보세요. 전문가가 설계한 코스로 잊지 못할 여행을 떠나세요.' => [
        'en' => 'Discover curated travel itineraries. Embark on an unforgettable journey with expertly designed routes.',
        'ja' => '特別な旅行プランをご覧ください。専門家が設計したコースで忘れられない旅に出かけましょう。',
        'zh-cn' => '发现精选旅行行程。跟随专家设计的路线，踏上难忘的旅程。',
        'zh-tw' => '發現精選旅行行程。跟隨專家設計的路線，踏上難忘的旅程。',
        'fr' => 'Découvrez des itinéraires de voyage sélectionnés. Partez pour un voyage inoubliable avec des parcours conçus par des experts.',
        'de' => 'Entdecken Sie kuratierte Reiserouten. Begeben Sie sich auf eine unvergessliche Reise mit von Experten entworfenen Routen.',
    ],
];

foreach (['ft_hero_title', 'ft_hero_subtitle'] as $mod_key) {
    add_filter("theme_mod_{$mod_key}", function ($value) use ($ft_hero_translations) {
        $lang = ft_get_current_lang();
        if ($lang === 'ko' || !isset($ft_hero_translations[$value])) return $value;

        $lang_fallback = ['en-au' => 'en', 'zh-hk' => 'zh-tw'];
        $lookup = $lang_fallback[$lang] ?? $lang;

        return $ft_hero_translations[$value][$lookup] ?? $value;
    });
}
