<?php
/**
 * 런타임 UI 번역 시스템
 *
 * .mo 파일 없이 Polylang 현재 언어에 맞춰 __() / esc_html_e() 출력을 자동 번역.
 * gettext 필터를 사용하므로 기존 템플릿 수정 불필요.
 *
 * 지원 언어: ko(기본), en, zh-cn, ja, fr, de
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
        '가성비'   => ['en' => 'Budget',    'ja' => 'お手頃',        'zh-cn' => '经济实惠', 'fr' => 'Économique',  'de' => 'Günstig'],
        '프리미엄' => ['en' => 'Premium',   'ja' => 'プレミアム',    'zh-cn' => '高端',     'fr' => 'Premium',     'de' => 'Premium'],
        '럭셔리'   => ['en' => 'Luxury',    'ja' => 'ラグジュアリー','zh-cn' => '奢华',     'fr' => 'Luxe',        'de' => 'Luxus'],

        // ── 난이도 라벨 ──
        '쉬움'   => ['en' => 'Easy',     'ja' => '簡単',   'zh-cn' => '简单', 'fr' => 'Facile',    'de' => 'Einfach'],
        '보통'   => ['en' => 'Moderate', 'ja' => '普通',   'zh-cn' => '适中', 'fr' => 'Modéré',    'de' => 'Mittel'],
        '어려움' => ['en' => 'Hard',     'ja' => '難しい', 'zh-cn' => '困难', 'fr' => 'Difficile', 'de' => 'Schwer'],

        // ── 빵크럼 ──
        '홈'       => ['en' => 'Home',              'ja' => 'ホーム',       'zh-cn' => '首页',     'fr' => 'Accueil',      'de' => 'Startseite'],
        '여행 일정' => ['en' => 'Travel Itineraries', 'ja' => '旅行プラン',  'zh-cn' => '旅行行程', 'fr' => 'Itinéraires',  'de' => 'Reiserouten'],

        // ── 사이드바 ──
        '여행 정보' => ['en' => 'Travel Info',          'ja' => '旅行情報',           'zh-cn' => '旅行信息',   'fr' => 'Infos voyage',          'de' => 'Reiseinfo'],
        '목적지'     => ['en' => 'Destination',          'ja' => '目的地',             'zh-cn' => '目的地',     'fr' => 'Destination',           'de' => 'Reiseziel'],
        '여행 기간' => ['en' => 'Duration',             'ja' => '旅行期間',           'zh-cn' => '旅行时间',   'fr' => 'Durée',                 'de' => 'Reisedauer'],
        '가격대'     => ['en' => 'Price Range',          'ja' => '価格帯',             'zh-cn' => '价格范围',   'fr' => 'Gamme de prix',         'de' => 'Preisklasse'],
        '난이도'     => ['en' => 'Difficulty',            'ja' => '難易度',             'zh-cn' => '难度',       'fr' => 'Difficulté',            'de' => 'Schwierigkeit'],
        '추천 시기' => ['en' => 'Best Season',           'ja' => 'おすすめ時期',       'zh-cn' => '推荐季节',   'fr' => 'Meilleure saison',      'de' => 'Beste Reisezeit'],
        '하이라이트' => ['en' => 'Highlights',            'ja' => 'ハイライト',         'zh-cn' => '亮点',       'fr' => 'Points forts',          'de' => 'Highlights'],
        '공유하기'   => ['en' => 'Share',                 'ja' => 'シェア',             'zh-cn' => '分享',       'fr' => 'Partager',              'de' => 'Teilen'],
        '관련 일정' => ['en' => 'Related Itineraries',   'ja' => '関連プラン',         'zh-cn' => '相关行程',   'fr' => 'Itinéraires similaires','de' => 'Ähnliche Routen'],

        // ── 싱글 일정 ──
        '일자별 일정' => ['en' => 'Daily Itinerary',  'ja' => '日別プラン',     'zh-cn' => '每日行程', 'fr' => 'Programme journalier', 'de' => 'Tagesplan'],
        '여행 스타일' => ['en' => 'Travel Style',      'ja' => '旅行スタイル',   'zh-cn' => '旅行风格', 'fr' => 'Style de voyage',      'de' => 'Reisestil'],

        // ── 일정 Day 템플릿 ──
        '추천:'             => ['en' => 'Recommended:', 'ja' => 'おすすめ:',         'zh-cn' => '推荐:',       'fr' => 'Recommandé :', 'de' => 'Empfohlen:'],
        '예약하기 →'        => ['en' => 'Book Now →',   'ja' => '予約する →',        'zh-cn' => '立即预订 →',  'fr' => 'Réserver →',   'de' => 'Buchen →'],
        '자세히 보기 →'     => ['en' => 'Learn More →', 'ja' => '詳しく見る →',      'zh-cn' => '了解更多 →',  'fr' => 'En savoir plus →', 'de' => 'Mehr erfahren →'],
        '이 날의 핵심 팁'   => ['en' => "Today's Key Tips", 'ja' => 'この日のポイント', 'zh-cn' => '今日重点提示', 'fr' => 'Conseils du jour', 'de' => 'Tipps des Tages'],
        '주요 장소:'        => ['en' => 'Key Places:',  'ja' => '主要スポット:',     'zh-cn' => '主要景点:',   'fr' => 'Lieux clés :',  'de' => 'Wichtige Orte:'],
        '💡 팁:'            => ['en' => '💡 Tip:',      'ja' => '💡 ヒント:',        'zh-cn' => '💡 提示:',    'fr' => '💡 Conseil :',  'de' => '💡 Tipp:'],

        // ── 아카이브 ──
        '어디로 떠나볼까요?' => [
            'en' => 'Where Will You Go?',
            'ja' => 'どこへ行きますか？',
            'zh-cn' => '想去哪里旅行？',
            'fr' => 'Où allez-vous ?',
            'de' => 'Wohin geht die Reise?',
        ],
        '전 세계 다양한 여행 코스를 탐색하고 나만의 완벽한 여행을 계획해보세요.' => [
            'en' => 'Explore diverse travel itineraries worldwide and plan your perfect trip.',
            'ja' => '世界各地の多様な旅行コースを探索し、あなただけの完璧な旅を計画しましょう。',
            'zh-cn' => '探索世界各地的多样旅行路线，规划您的完美之旅。',
            'fr' => 'Explorez des itinéraires variés dans le monde entier et planifiez votre voyage parfait.',
            'de' => 'Entdecken Sie vielfältige Reiserouten weltweit und planen Sie Ihre perfekte Reise.',
        ],
        '도시, 국가 또는 여행 스타일 검색...' => [
            'en' => 'Search city, country or travel style...',
            'ja' => '都市、国、旅行スタイルで検索...',
            'zh-cn' => '搜索城市、国家或旅行风格...',
            'fr' => 'Rechercher ville, pays ou style...',
            'de' => 'Stadt, Land oder Reisestil suchen...',
        ],
        '검색'   => ['en' => 'Search',       'ja' => '検索',     'zh-cn' => '搜索',   'fr' => 'Rechercher',  'de' => 'Suchen'],
        '여행지' => ['en' => 'Destinations',  'ja' => '旅行先',   'zh-cn' => '目的地', 'fr' => 'Destinations', 'de' => 'Reiseziele'],
        '스타일' => ['en' => 'Style',         'ja' => 'スタイル', 'zh-cn' => '风格',   'fr' => 'Style',        'de' => 'Stil'],
        '전체'   => ['en' => 'All',           'ja' => 'すべて',   'zh-cn' => '全部',   'fr' => 'Tout',         'de' => 'Alle'],

        // ── 여행지 그리드 ──
        '인기 여행지'  => ['en' => 'Popular Destinations', 'ja' => '人気の旅行先',     'zh-cn' => '热门目的地', 'fr' => 'Destinations populaires', 'de' => 'Beliebte Reiseziele'],
        '%d개의 일정'  => ['en' => '%d itineraries',       'ja' => '%d件のプラン',      'zh-cn' => '%d个行程',   'fr' => '%d itinéraires',          'de' => '%d Reiserouten'],

        // ── 검색 ──
        '"%s" 검색 결과' => [
            'en' => 'Search results for "%s"',
            'ja' => '「%s」の検索結果',
            'zh-cn' => '"%s"的搜索结果',
            'fr' => 'Résultats pour « %s »',
            'de' => 'Suchergebnisse für „%s"',
        ],

        // ── 읽기 시간 ──
        '%d분 읽기' => ['en' => '%d min read', 'ja' => '%d分で読める', 'zh-cn' => '%d分钟阅读', 'fr' => '%d min de lecture', 'de' => '%d Min. Lesezeit'],

        // ── ARIA 라벨 ──
        '페이지 네비게이션' => ['en' => 'Page navigation',       'ja' => 'ページナビゲーション',     'zh-cn' => '页面导航',   'fr' => 'Navigation de page',  'de' => 'Seitennavigation'],
        '빵크럼 네비게이션' => ['en' => 'Breadcrumb navigation', 'ja' => 'パンくずナビゲーション',   'zh-cn' => '面包屑导航', 'fr' => "Fil d'Ariane",        'de' => 'Brotkrümelnavigation'],

        // ── 메인 메뉴 / 헤더 / 푸터 ──
        '메인 메뉴'  => ['en' => 'Main Menu',   'ja' => 'メインメニュー', 'zh-cn' => '主菜单',   'fr' => 'Menu principal', 'de' => 'Hauptmenü'],
        '푸터 메뉴'  => ['en' => 'Footer Menu', 'ja' => 'フッターメニュー', 'zh-cn' => '页脚菜单', 'fr' => 'Menu pied de page', 'de' => 'Fußzeilenmenü'],
        '본문으로 건너뛰기' => ['en' => 'Skip to content', 'ja' => '本文へスキップ', 'zh-cn' => '跳到正文', 'fr' => 'Aller au contenu', 'de' => 'Zum Inhalt springen'],
        '메뉴 열기'  => ['en' => 'Open menu', 'ja' => 'メニューを開く', 'zh-cn' => '打开菜单', 'fr' => 'Ouvrir le menu', 'de' => 'Menü öffnen'],
        '메뉴'       => ['en' => 'Menu',      'ja' => 'メニュー',       'zh-cn' => '菜单',     'fr' => 'Menu',            'de' => 'Menü'],
        'All rights reserved.' => ['en' => 'All rights reserved.', 'ja' => 'All rights reserved.', 'zh-cn' => '保留所有权利。', 'fr' => 'Tous droits réservés.', 'de' => 'Alle Rechte vorbehalten.'],

        // ── 홈페이지 ──
        '추천 여행 일정' => ['en' => 'Featured Itineraries', 'ja' => 'おすすめ旅行プラン', 'zh-cn' => '推荐旅行行程', 'fr' => 'Itinéraires recommandés', 'de' => 'Empfohlene Reiserouten'],
        '엄선된 여행 코스를 만나보세요' => ['en' => 'Discover curated travel plans', 'ja' => '厳選された旅行コースをご覧ください', 'zh-cn' => '发现精选旅行路线', 'fr' => 'Découvrez nos itinéraires sélectionnés', 'de' => 'Entdecken Sie ausgewählte Reiserouten'],
        '아직 등록된 여행 일정이 없습니다.' => ['en' => 'No itineraries available yet.', 'ja' => 'まだ旅行プランがありません。', 'zh-cn' => '暂无旅行行程。', 'fr' => 'Aucun itinéraire disponible.', 'de' => 'Noch keine Reiserouten verfügbar.'],
        '모든 일정 보기 →' => ['en' => 'View All Itineraries →', 'ja' => 'すべてのプランを見る →', 'zh-cn' => '查看所有行程 →', 'fr' => 'Voir tous les itinéraires →', 'de' => 'Alle Reiserouten ansehen →'],
        '여행 이야기' => ['en' => 'Travel Stories', 'ja' => '旅の物語', 'zh-cn' => '旅行故事', 'fr' => 'Récits de voyage', 'de' => 'Reisegeschichten'],
        '생생한 여행 후기와 팁을 공유합니다' => ['en' => 'Real travel reviews and tips', 'ja' => 'リアルな旅行レビューとヒント', 'zh-cn' => '分享真实旅行评价和攻略', 'fr' => 'Avis et conseils de voyageurs', 'de' => 'Echte Reiseberichte und Tipps'],
        '아직 등록된 글이 없습니다.' => ['en' => 'No posts available yet.', 'ja' => 'まだ投稿がありません。', 'zh-cn' => '暂无文章。', 'fr' => 'Aucun article disponible.', 'de' => 'Noch keine Beiträge verfügbar.'],

        // ── 히어로 섹션 ──
        '나만의 여행 일정 만들기' => ['en' => 'Create Your Own Itinerary', 'ja' => '自分だけの旅行プランを作る', 'zh-cn' => '创建专属旅行行程', 'fr' => 'Créez votre itinéraire', 'de' => 'Eigene Reiseroute erstellen'],

        // ── 결과 없음 ──
        '결과가 없습니다' => ['en' => 'No Results Found', 'ja' => '結果が見つかりません', 'zh-cn' => '未找到结果', 'fr' => 'Aucun résultat', 'de' => 'Keine Ergebnisse'],
        '검색어와 일치하는 결과가 없습니다. 다른 키워드로 다시 검색해보세요.' => [
            'en' => 'No results match your search. Try different keywords.',
            'ja' => '検索に一致する結果がありません。別のキーワードで再検索してください。',
            'zh-cn' => '没有匹配的搜索结果，请尝试其他关键词。',
            'fr' => 'Aucun résultat ne correspond à votre recherche. Essayez d\'autres mots-clés.',
            'de' => 'Keine Ergebnisse gefunden. Versuchen Sie andere Suchbegriffe.',
        ],
        '아직 게시된 콘텐츠가 없습니다.' => ['en' => 'No content available yet.', 'ja' => 'まだコンテンツがありません。', 'zh-cn' => '暂无内容。', 'fr' => 'Aucun contenu disponible.', 'de' => 'Noch keine Inhalte verfügbar.'],

        // ── 도시 가이드 ──
        '도시 가이드' => ['en' => 'City Guide', 'ja' => '都市ガイド', 'zh-cn' => '城市指南', 'fr' => 'Guide de la ville', 'de' => 'Stadtführer'],
        '여행 스타일별 관광지/맛집/호텔 비교' => [
            'en' => 'Compare attractions, restaurants & hotels by travel style',
            'ja' => '旅行スタイル別の観光地・グルメ・ホテル比較',
            'zh-cn' => '按旅行风格比较景点/餐厅/酒店',
            'fr' => 'Comparez attractions, restaurants et hôtels par style de voyage',
            'de' => 'Sehenswürdigkeiten, Restaurants & Hotels nach Reisestil vergleichen',
        ],
        '관광지' => ['en' => 'Attractions', 'ja' => '観光地', 'zh-cn' => '景点', 'fr' => 'Attractions', 'de' => 'Sehenswürdigkeiten'],
        '식당'   => ['en' => 'Restaurants', 'ja' => 'レストラン', 'zh-cn' => '餐厅', 'fr' => 'Restaurants', 'de' => 'Restaurants'],
        '호텔'   => ['en' => 'Hotels', 'ja' => 'ホテル', 'zh-cn' => '酒店', 'fr' => 'Hôtels', 'de' => 'Hotels'],
        '이름'   => ['en' => 'Name', 'ja' => '名前', 'zh-cn' => '名称', 'fr' => 'Nom', 'de' => 'Name'],
        '지역'   => ['en' => 'Area', 'ja' => 'エリア', 'zh-cn' => '地区', 'fr' => 'Zone', 'de' => 'Gebiet'],
        '카테고리' => ['en' => 'Category', 'ja' => 'カテゴリー', 'zh-cn' => '分类', 'fr' => 'Catégorie', 'de' => 'Kategorie'],
        '음식'   => ['en' => 'Cuisine', 'ja' => '料理', 'zh-cn' => '菜系', 'fr' => 'Cuisine', 'de' => 'Küche'],
        '가격'   => ['en' => 'Price', 'ja' => '価格', 'zh-cn' => '价格', 'fr' => 'Prix', 'de' => 'Preis'],
        '등급'   => ['en' => 'Grade', 'ja' => 'グレード', 'zh-cn' => '等级', 'fr' => 'Catégorie', 'de' => 'Klasse'],
        '메모'   => ['en' => 'Note', 'ja' => 'メモ', 'zh-cn' => '备注', 'fr' => 'Note', 'de' => 'Hinweis'],
        '가족'   => ['en' => 'Family', 'ja' => '家族', 'zh-cn' => '家庭', 'fr' => 'Famille', 'de' => 'Familie'],
        '커플'   => ['en' => 'Couple', 'ja' => 'カップル', 'zh-cn' => '情侣', 'fr' => 'Couple', 'de' => 'Paar'],
        '솔로'   => ['en' => 'Solo', 'ja' => 'ソロ', 'zh-cn' => '独行', 'fr' => 'Solo', 'de' => 'Solo'],
        '친구'   => ['en' => 'Friends', 'ja' => '友達', 'zh-cn' => '朋友', 'fr' => 'Amis', 'de' => 'Freunde'],
        '효도'   => ['en' => 'Filial', 'ja' => '親孝行', 'zh-cn' => '孝行', 'fr' => 'Piété filiale', 'de' => 'Familienehrung'],
        '정렬하려면 열 제목을 클릭하세요' => [
            'en' => 'Click column headers to sort',
            'ja' => '列タイトルをクリックしてソート',
            'zh-cn' => '点击列标题排序',
            'fr' => 'Cliquez sur les en-têtes pour trier',
            'de' => 'Klicken Sie auf Spaltenüberschriften zum Sortieren',
        ],
        '꼭 해볼 것' => ['en' => 'Must Do', 'ja' => '必ずやるべきこと', 'zh-cn' => '必做之事', 'fr' => 'Incontournables', 'de' => 'Muss man machen'],
        '인기 메뉴' => ['en' => 'Popular Menu', 'ja' => '人気メニュー', 'zh-cn' => '人气菜单', 'fr' => 'Menu populaire', 'de' => 'Beliebtes Menü'],
        '상세 정보' => ['en' => 'Details', 'ja' => '詳細情報', 'zh-cn' => '详细信息', 'fr' => 'Détails', 'de' => 'Details'],
        '구글맵에서 보기' => ['en' => 'View on Google Maps', 'ja' => 'Googleマップで見る', 'zh-cn' => '在谷歌地图查看', 'fr' => 'Voir sur Google Maps', 'de' => 'Auf Google Maps ansehen'],
        '예약/입장권 보기' => ['en' => 'Search on Klook', 'ja' => 'Klookで検索', 'zh-cn' => '在Klook搜索', 'fr' => 'Rechercher sur Klook', 'de' => 'Auf Klook suchen'],
        'Klook에서 검색' => ['en' => 'Search on Klook', 'ja' => 'Klookで検索', 'zh-cn' => '在Klook搜索', 'fr' => 'Rechercher sur Klook', 'de' => 'Auf Klook suchen'],
        'Klook에서 검색 →' => ['en' => 'Search on Klook →', 'ja' => 'Klookで検索 →', 'zh-cn' => '在Klook搜索 →', 'fr' => 'Rechercher sur Klook →', 'de' => 'Auf Klook suchen →'],
        '모든 가이드 보기 →' => ['en' => 'View All Guides →', 'ja' => 'すべてのガイドを見る →', 'zh-cn' => '查看所有指南 →', 'fr' => 'Voir tous les guides →', 'de' => 'Alle Guides ansehen →'],
        '액티비티' => ['en' => 'Activities', 'ja' => 'アクティビティ', 'zh-cn' => '活动', 'fr' => 'Activités', 'de' => 'Aktivitäten'],
        '%s에서 즐길 수 있는 액티비티, 투어, 입장권을 Klook에서 찾아보세요.' => ['en' => 'Find activities, tours, and tickets in %s on Klook.', 'ja' => '%sで楽しめるアクティビティ、ツアー、チケットをKlookで探してみましょう。', 'zh-cn' => '在Klook上查找%s的活动、旅游和门票。', 'fr' => 'Trouvez des activités, visites et billets à %s sur Klook.', 'de' => 'Finden Sie Aktivitäten, Touren und Tickets in %s auf Klook.'],
        '%s 전체 액티비티 검색' => ['en' => 'Search all %s activities', 'ja' => '%s の全アクティビティを検索', 'zh-cn' => '搜索%s全部活动', 'fr' => 'Rechercher toutes les activités à %s', 'de' => 'Alle Aktivitäten in %s suchen'],
        '카테고리별 검색' => ['en' => 'Browse by Category', 'ja' => 'カテゴリで探す', 'zh-cn' => '按类别搜索', 'fr' => 'Rechercher par catégorie', 'de' => 'Nach Kategorie suchen'],
        '인기 명소 액티비티' => ['en' => 'Top Attraction Activities', 'ja' => '人気スポットのアクティビティ', 'zh-cn' => '热门景点活动', 'fr' => 'Activités des sites populaires', 'de' => 'Aktivitäten beliebter Sehenswürdigkeiten'],
        '관광지 입장권' => ['en' => 'Attraction Tickets', 'ja' => '観光地チケット', 'zh-cn' => '景点门票', 'fr' => 'Billets d\'entrée', 'de' => 'Eintrittskarten'],
        '투어 & 데이트립' => ['en' => 'Tours & Day Trips', 'ja' => 'ツアー＆日帰り旅行', 'zh-cn' => '旅游和一日游', 'fr' => 'Visites & excursions', 'de' => 'Touren & Tagesausflüge'],
        '맛집 & 푸드투어' => ['en' => 'Food & Food Tours', 'ja' => 'グルメ＆フードツアー', 'zh-cn' => '美食和美食之旅', 'fr' => 'Gastronomie & food tours', 'de' => 'Essen & Food-Touren'],
        '체험 & 클래스' => ['en' => 'Experiences & Classes', 'ja' => '体験＆クラス', 'zh-cn' => '体验和课程', 'fr' => 'Expériences & cours', 'de' => 'Erlebnisse & Kurse'],
        '교통 패스' => ['en' => 'Transport Passes', 'ja' => '交通パス', 'zh-cn' => '交通通票', 'fr' => 'Pass transport', 'de' => 'Verkehrspässe'],
        'SIM카드 & 와이파이' => ['en' => 'SIM Cards & WiFi', 'ja' => 'SIMカード＆WiFi', 'zh-cn' => 'SIM卡和WiFi', 'fr' => 'Cartes SIM & WiFi', 'de' => 'SIM-Karten & WiFi'],
        '테마파크' => ['en' => 'Theme Parks', 'ja' => 'テーマパーク', 'zh-cn' => '主题公园', 'fr' => 'Parcs à thème', 'de' => 'Freizeitparks'],
        '스파 & 웰니스' => ['en' => 'Spa & Wellness', 'ja' => 'スパ＆ウェルネス', 'zh-cn' => '水疗和健康', 'fr' => 'Spa & bien-être', 'de' => 'Spa & Wellness'],
        '%d곳' => ['en' => '%d', 'ja' => '%d件', 'zh-cn' => '%d个', 'fr' => '%d', 'de' => '%d'],
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

    return $strings[$text][$lang] ?? $translated;
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

    return $strings[$text][$lang] ?? $translated;
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
        'fr' => 'Le début d\'un voyage savoureux',
        'de' => 'Der Beginn einer köstlichen Reise',
    ],
    '특별한 여행 일정을 만나보세요. 전문가가 설계한 코스로 잊지 못할 여행을 떠나세요.' => [
        'en' => 'Discover curated travel itineraries. Embark on an unforgettable journey with expertly designed routes.',
        'ja' => '特別な旅行プランをご覧ください。専門家が設計したコースで忘れられない旅に出かけましょう。',
        'zh-cn' => '发现精选旅行行程。跟随专家设计的路线，踏上难忘的旅程。',
        'fr' => 'Découvrez des itinéraires de voyage sélectionnés. Partez pour un voyage inoubliable avec des parcours conçus par des experts.',
        'de' => 'Entdecken Sie kuratierte Reiserouten. Begeben Sie sich auf eine unvergessliche Reise mit von Experten entworfenen Routen.',
    ],
];

foreach (['ft_hero_title', 'ft_hero_subtitle'] as $mod_key) {
    add_filter("theme_mod_{$mod_key}", function ($value) use ($ft_hero_translations) {
        $lang = ft_get_current_lang();
        if ($lang === 'ko' || !isset($ft_hero_translations[$value])) return $value;

        return $ft_hero_translations[$value][$lang] ?? $value;
    });
}

/**
 * bloginfo() 값 번역 (사이트 설명, 사이트명)
 * bloginfo('description') / bloginfo('name') 은 gettext 필터를 거치지 않으므로
 * option_blogdescription / option_blogname 필터로 직접 처리
 */
$ft_bloginfo_translations = [
    'blogdescription' => [
        '소중한 사람들과 행복한 추억을 만들 수 있는 여행을 해보세요.' => [
            'en' => 'Travel with your loved ones and create unforgettable memories.',
            'ja' => '大切な人と幸せな思い出を作る旅に出かけましょう。',
            'zh-cn' => '与珍爱的人一起旅行，创造美好回忆。',
            'fr' => 'Voyagez avec vos proches et créez des souvenirs inoubliables.',
            'de' => 'Reisen Sie mit Ihren Liebsten und schaffen Sie unvergessliche Erinnerungen.',
        ],
    ],
];

foreach ($ft_bloginfo_translations as $option_name => $translations) {
    add_filter("option_{$option_name}", function ($value) use ($translations) {
        $lang = ft_get_current_lang();
        if ($lang === 'ko' || !isset($translations[$value])) return $value;

        return $translations[$value][$lang] ?? $value;
    });
}
