/**
 * TripTalk — 지도 초기화
 * 카카오맵(기본) / 구글맵(폴백)
 */

(function () {
    'use strict';

    var mapContainer = document.getElementById('ft-map');
    if (!mapContainer) return;

    var lat = parseFloat(mapContainer.dataset.lat);
    var lng = parseFloat(mapContainer.dataset.lng);
    var zoom = parseInt(mapContainer.dataset.zoom, 10) || 12;
    var title = mapContainer.dataset.title || '';

    if (isNaN(lat) || isNaN(lng)) return;

    var config = window.ftMapConfig || {};

    function initKakaoMap() {
        if (typeof kakao === 'undefined' || !kakao.maps) {
            mapContainer.innerHTML = '<p style="padding:20px;text-align:center;color:#6b7280;">카카오맵을 로드할 수 없습니다.</p>';
            return;
        }

        kakao.maps.load(function () {
            var options = {
                center: new kakao.maps.LatLng(lat, lng),
                level: Math.max(1, Math.min(14, 15 - zoom)), // 카카오맵 레벨 변환
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

            // 리사이즈 대응
            window.addEventListener('resize', function () {
                map.relayout();
            });
        });
    }

    function initGoogleMap() {
        if (typeof google === 'undefined' || !google.maps) {
            mapContainer.innerHTML = '<p style="padding:20px;text-align:center;color:#6b7280;">구글맵을 로드할 수 없습니다.</p>';
            return;
        }

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

    // 초기화
    if (config.provider === 'kakao') {
        // 카카오맵 SDK가 비동기 로드되므로 대기
        if (typeof kakao !== 'undefined' && kakao.maps) {
            initKakaoMap();
        } else {
            // SDK 로드 후 초기화
            var checkKakao = setInterval(function () {
                if (typeof kakao !== 'undefined' && kakao.maps) {
                    clearInterval(checkKakao);
                    initKakaoMap();
                }
            }, 100);
            // 5초 타임아웃
            setTimeout(function () { clearInterval(checkKakao); }, 5000);
        }
    } else if (config.provider === 'google') {
        if (typeof google !== 'undefined' && google.maps) {
            initGoogleMap();
        } else {
            var checkGoogle = setInterval(function () {
                if (typeof google !== 'undefined' && google.maps) {
                    clearInterval(checkGoogle);
                    initGoogleMap();
                }
            }, 100);
            setTimeout(function () { clearInterval(checkGoogle); }, 5000);
        }
    } else {
        mapContainer.innerHTML = '<p style="padding:20px;text-align:center;color:#6b7280;">지도 API 키가 설정되지 않았습니다. 커스터마이저에서 카카오맵 또는 구글맵 API 키를 입력하세요.</p>';
    }
})();
