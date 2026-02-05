/**
 * Flavor Trip — 지도 초기화
 * spots 동선(Day별 마커+Polyline) + 단일 마커 폴백
 */

(function () {
    'use strict';

    var mapContainer = document.getElementById('ft-map');
    if (!mapContainer) return;

    var lat = parseFloat(mapContainer.dataset.lat);
    var lng = parseFloat(mapContainer.dataset.lng);
    var zoom = parseInt(mapContainer.dataset.zoom, 10) || 12;
    var title = mapContainer.dataset.title || '';
    var spotsJSON = mapContainer.dataset.spots;
    var spots = [];

    try { if (spotsJSON) spots = JSON.parse(spotsJSON); } catch (e) { spots = []; }

    var config = window.ftMapConfig || {};

    // Day별 accent 색상
    var dayColors = {
        1: '#2563eb',
        2: '#10b981',
        3: '#f59e0b',
        4: '#8b5cf6',
        5: '#ef4444',
        6: '#06b6d4'
    };

    function getDayColor(day) {
        return dayColors[day] || '#6b7280';
    }

    // ── Google Maps ──
    function initGoogleMap() {
        if (typeof google === 'undefined' || !google.maps) {
            mapContainer.innerHTML = '<p style="padding:20px;text-align:center;color:#6b7280;">구글맵을 로드할 수 없습니다.</p>';
            return;
        }

        if (spots.length > 0) {
            initGoogleSpotsMap();
        } else if (!isNaN(lat) && !isNaN(lng)) {
            initGoogleSingleMap();
        }
    }

    function initGoogleSpotsMap() {
        var map = new google.maps.Map(mapContainer, {
            zoom: zoom,
            center: { lat: spots[0].lat, lng: spots[0].lng },
            mapTypeControl: false,
            streetViewControl: false,
        });

        var bounds = new google.maps.LatLngBounds();
        var infoWindow = new google.maps.InfoWindow();

        // Day별로 그룹핑
        var dayGroups = {};
        spots.forEach(function (s) {
            if (!dayGroups[s.day]) dayGroups[s.day] = [];
            dayGroups[s.day].push(s);
        });

        // 마커 + Polyline
        Object.keys(dayGroups).forEach(function (day) {
            var group = dayGroups[day];
            var color = getDayColor(parseInt(day));
            var path = [];

            group.forEach(function (s) {
                var pos = { lat: s.lat, lng: s.lng };
                bounds.extend(pos);
                path.push(pos);

                var marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    label: {
                        text: String(s.n),
                        color: '#fff',
                        fontSize: '11px',
                        fontWeight: '700',
                    },
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 14,
                        fillColor: color,
                        fillOpacity: 1,
                        strokeColor: '#fff',
                        strokeWeight: 2,
                    },
                    title: s.name,
                });

                marker.addListener('click', function () {
                    infoWindow.setContent(
                        '<div style="padding:4px 8px;font-size:13px;"><strong>Day ' + s.day + '-' + s.n + '</strong> ' + s.name + '</div>'
                    );
                    infoWindow.open(map, marker);
                });
            });

            // Day별 동선 Polyline
            if (path.length > 1) {
                new google.maps.Polyline({
                    path: path,
                    geodesic: true,
                    strokeColor: color,
                    strokeOpacity: 0.6,
                    strokeWeight: 3,
                    map: map,
                });
            }
        });

        map.fitBounds(bounds);
        // 너무 줌인 방지
        google.maps.event.addListenerOnce(map, 'bounds_changed', function () {
            if (map.getZoom() > 15) map.setZoom(15);
        });
    }

    function initGoogleSingleMap() {
        var center = { lat: lat, lng: lng };
        var map = new google.maps.Map(mapContainer, {
            center: center,
            zoom: zoom,
        });

        var marker = new google.maps.Marker({
            position: center,
            map: map,
            title: title,
        });

        if (title) {
            var infowindow = new google.maps.InfoWindow({
                content: '<div style="padding:4px;font-size:13px;">' + title + '</div>',
            });
            infowindow.open(map, marker);
        }
    }

    // ── Kakao Maps ──
    function initKakaoMap() {
        if (typeof kakao === 'undefined' || !kakao.maps) {
            mapContainer.innerHTML = '<p style="padding:20px;text-align:center;color:#6b7280;">카카오맵을 로드할 수 없습니다.</p>';
            return;
        }

        kakao.maps.load(function () {
            if (spots.length > 0) {
                initKakaoSpotsMap();
            } else if (!isNaN(lat) && !isNaN(lng)) {
                initKakaoSingleMap();
            }
        });
    }

    function initKakaoSpotsMap() {
        var options = {
            center: new kakao.maps.LatLng(spots[0].lat, spots[0].lng),
            level: 7,
        };
        var map = new kakao.maps.Map(mapContainer, options);
        var bounds = new kakao.maps.LatLngBounds();
        var infoWindow = new kakao.maps.InfoWindow({ zIndex: 1 });

        var dayGroups = {};
        spots.forEach(function (s) {
            if (!dayGroups[s.day]) dayGroups[s.day] = [];
            dayGroups[s.day].push(s);
        });

        Object.keys(dayGroups).forEach(function (day) {
            var group = dayGroups[day];
            var color = getDayColor(parseInt(day));
            var path = [];

            group.forEach(function (s) {
                var pos = new kakao.maps.LatLng(s.lat, s.lng);
                bounds.extend(pos);
                path.push(pos);

                var marker = new kakao.maps.Marker({
                    position: pos,
                    map: map,
                });

                kakao.maps.event.addListener(marker, 'click', function () {
                    infoWindow.setContent(
                        '<div style="padding:8px 12px;font-size:13px;white-space:nowrap;"><strong>Day ' + s.day + '-' + s.n + '</strong> ' + s.name + '</div>'
                    );
                    infoWindow.open(map, marker);
                });
            });

            if (path.length > 1) {
                new kakao.maps.Polyline({
                    path: path,
                    strokeWeight: 3,
                    strokeColor: color,
                    strokeOpacity: 0.6,
                    strokeStyle: 'solid',
                    map: map,
                });
            }
        });

        map.setBounds(bounds);
        window.addEventListener('resize', function () { map.relayout(); });
    }

    function initKakaoSingleMap() {
        var options = {
            center: new kakao.maps.LatLng(lat, lng),
            level: Math.max(1, Math.min(14, 15 - zoom)),
        };

        var map = new kakao.maps.Map(mapContainer, options);

        var marker = new kakao.maps.Marker({
            position: new kakao.maps.LatLng(lat, lng),
            map: map,
        });

        if (title) {
            var infowindow = new kakao.maps.InfoWindow({
                content: '<div style="padding:8px 12px;font-size:13px;white-space:nowrap;">' + title + '</div>',
            });
            infowindow.open(map, marker);
        }

        window.addEventListener('resize', function () { map.relayout(); });
    }

    // ── 초기화 ──
    function waitAndInit(checkFn, initFn) {
        if (checkFn()) {
            initFn();
        } else {
            var check = setInterval(function () {
                if (checkFn()) {
                    clearInterval(check);
                    initFn();
                }
            }, 100);
            setTimeout(function () { clearInterval(check); }, 5000);
        }
    }

    if (config.provider === 'kakao') {
        waitAndInit(
            function () { return typeof kakao !== 'undefined' && kakao.maps; },
            initKakaoMap
        );
    } else if (config.provider === 'google') {
        waitAndInit(
            function () { return typeof google !== 'undefined' && google.maps; },
            initGoogleMap
        );
    } else {
        mapContainer.innerHTML = '<p style="padding:20px;text-align:center;color:#6b7280;">지도 API 키가 설정되지 않았습니다. 커스터마이저에서 카카오맵 또는 구글맵 API 키를 입력하세요.</p>';
    }
})();
