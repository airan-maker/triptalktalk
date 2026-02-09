/**
 * Destination Guide — 구글맵 통합
 *
 * 카테고리별 마커, InfoWindow, 탭 필터, 테이블 연동
 *
 * @package Flavor_Trip
 */
var _ftGuideMapDone = false;
function initFtGuideMap() {
    'use strict';
    if (_ftGuideMapDone) return;
    _ftGuideMapDone = true;

    var mapEl = document.getElementById('ft-guide-map');
    if (!mapEl) return;

    var config = window.ftGuideMap || {};
    var items  = config.items || [];
    var labels = config.labels || {};
    var klookAid = config.klookAid || '';

    if (!items.length) return;

    // 카테고리별 마커 색상
    var COLORS = {
        places:      '#2563eb',
        restaurants: '#f59e0b',
        hotels:      '#8b5cf6'
    };

    // SVG 마커 아이콘 생성
    function makeIcon(color) {
        var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="40" viewBox="0 0 28 40">'
            + '<path d="M14 0C6.27 0 0 6.27 0 14c0 10.5 14 26 14 26s14-15.5 14-26C28 6.27 21.73 0 14 0z" fill="' + color + '"/>'
            + '<circle cx="14" cy="14" r="6" fill="#fff"/>'
            + '</svg>';
        return {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
            scaledSize: new google.maps.Size(28, 40),
            anchor: new google.maps.Point(14, 40)
        };
    }

    // 지도 초기화
    var bounds = new google.maps.LatLngBounds();
    var map = new google.maps.Map(mapEl, {
        zoom: 13,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        styles: [
            { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] }
        ]
    });

    var infoWindow = new google.maps.InfoWindow();
    var markers = [];
    var activeTab = 'places';

    // 별점 HTML 생성
    function starsHtml(rating) {
        var html = '';
        for (var i = 1; i <= 5; i++) {
            html += '<span style="color:' + (i <= rating ? '#f59e0b' : '#e2e8f0') + '">★</span>';
        }
        return html;
    }

    // InfoWindow 컨텐츠 생성
    function buildContent(item) {
        var type = item._type;
        var html = '<div class="guide-info-window">';
        html += '<h3 class="giw-title">' + esc(item.name) + '</h3>';

        // 카테고리/음식/등급 + 지역
        var meta = [];
        if (item.category) meta.push(esc(item.category));
        if (item.cuisine) meta.push(esc(item.cuisine));
        if (item.grade) meta.push(esc(item.grade));
        if (item.area) meta.push(esc(item.area));
        if (item.price) meta.push('<strong>' + esc(item.price) + '</strong>');
        if (meta.length) {
            html += '<p class="giw-meta">' + meta.join(' · ') + '</p>';
        }

        // 상세 설명
        if (item.detail) {
            html += '<p class="giw-detail">' + esc(item.detail) + '</p>';
        }

        // 꼭 해볼 것
        if (item.must_do) {
            html += '<div class="giw-section"><strong>' + esc(labels.must_do || '꼭 해볼 것') + '</strong><p>' + esc(item.must_do) + '</p></div>';
        }

        // 인기 메뉴 (식당만)
        if (item.popular_menu && type === 'restaurants') {
            html += '<div class="giw-section"><strong>' + esc(labels.popular_menu || '인기 메뉴') + '</strong><p>' + esc(item.popular_menu) + '</p></div>';
        }

        // 별점
        var ratingKeys = ['family', 'couple', 'solo', 'friends', 'filial'];
        var ratingLabels = {
            family: labels.family || '가족',
            couple: labels.couple || '커플',
            solo: labels.solo || '솔로',
            friends: labels.friends || '친구',
            filial: labels.filial || '효도'
        };
        html += '<div class="giw-ratings">';
        for (var i = 0; i < ratingKeys.length; i++) {
            var key = ratingKeys[i];
            var val = parseInt(item[key]) || 0;
            html += '<span class="giw-rating">' + esc(ratingLabels[key]) + ' ' + starsHtml(val) + '</span>';
        }
        html += '</div>';

        // 메모
        if (item.note) {
            html += '<p class="giw-note">' + esc(item.note) + '</p>';
        }

        // 링크
        html += '<div class="giw-links">';
        html += '<a href="https://www.google.com/maps?q=' + item.lat + ',' + item.lng + '" target="_blank" rel="noopener noreferrer" class="giw-link giw-link--map">📍 ' + esc(labels.view_on_map || '구글맵에서 보기') + '</a>';

        if (item.klook_url) {
            var kUrl = item.klook_url;
            if (klookAid) {
                kUrl += (kUrl.indexOf('?') === -1 ? '?' : '&') + 'aid=' + klookAid;
            }
            html += '<a href="' + esc(kUrl) + '" target="_blank" rel="noopener noreferrer nofollow sponsored" class="giw-link giw-link--klook">🎫 ' + esc(labels.book_ticket || '예약/입장권 보기') + '</a>';
        }
        html += '</div>';

        html += '</div>';
        return html;
    }

    // 간단한 HTML 이스케이프
    function esc(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    // 마커 생성
    items.forEach(function (item, idx) {
        var lat = parseFloat(item.lat);
        var lng = parseFloat(item.lng);
        if (isNaN(lat) || isNaN(lng)) return;

        var pos = new google.maps.LatLng(lat, lng);
        bounds.extend(pos);

        var marker = new google.maps.Marker({
            position: pos,
            map: map,
            title: item.name,
            icon: makeIcon(COLORS[item._type] || '#6b7280'),
            visible: item._type === activeTab
        });

        marker._type = item._type;
        marker._index = idx;
        marker._item = item;

        marker.addListener('click', function () {
            infoWindow.setContent(buildContent(item));
            infoWindow.open(map, marker);
        });

        markers.push(marker);
    });

    // 지도 범위 조정 (현재 탭 기준)
    function fitBoundsForTab(tab) {
        var b = new google.maps.LatLngBounds();
        var count = 0;
        markers.forEach(function (m) {
            if (m._type === tab) {
                b.extend(m.getPosition());
                count++;
            }
        });
        if (count > 0) {
            map.fitBounds(b);
            if (count === 1) map.setZoom(15);
        }
    }

    // 초기 범위 설정
    fitBoundsForTab('places');

    // 탭 전환 → 마커 필터 + 범위 조정
    function onTabChange(tab) {
        activeTab = tab;
        infoWindow.close();
        markers.forEach(function (m) {
            m.setVisible(m._type === tab);
        });
        fitBoundsForTab(tab);
    }

    // 탭 버튼 이벤트 감시
    document.querySelectorAll('.guide-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = this.getAttribute('data-tab');
            onTabChange(tab);
        });
    });

    // 테이블 행 클릭 → 맵 스크롤 + InfoWindow
    document.querySelectorAll('.guide-table tr[data-lat]').forEach(function (row) {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function () {
            var lat = parseFloat(this.getAttribute('data-lat'));
            var lng = parseFloat(this.getAttribute('data-lng'));
            if (isNaN(lat) || isNaN(lng)) return;

            // 해당 마커 찾기
            for (var i = 0; i < markers.length; i++) {
                var m = markers[i];
                var pos = m.getPosition();
                if (Math.abs(pos.lat() - lat) < 0.0001 && Math.abs(pos.lng() - lng) < 0.0001) {
                    map.panTo(pos);
                    map.setZoom(16);
                    infoWindow.setContent(buildContent(m._item));
                    infoWindow.open(map, m);

                    // 맵으로 스크롤
                    mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    break;
                }
            }
        });
    });

}
