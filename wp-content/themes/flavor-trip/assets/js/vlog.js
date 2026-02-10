/**
 * Vlog Curation — 유튜브 Lazy Embed + 타임라인 + 지도
 *
 * @package Flavor_Trip
 */

(function () {
    'use strict';

    // ── YouTube IFrame API ──
    var player = null;
    var playerReady = false;

    /**
     * 타임스탬프 문자열을 초 단위로 변환
     * "3:45" → 225, "1:02:30" → 3750
     */
    function parseTimestamp(str) {
        var parts = str.split(':').map(Number);
        if (parts.length === 3) return parts[0] * 3600 + parts[1] * 60 + parts[2];
        if (parts.length === 2) return parts[0] * 60 + parts[1];
        return parts[0] || 0;
    }

    /**
     * Lazy Embed: 썸네일 클릭 → iframe 교체
     */
    function initLazyEmbed() {
        var container = document.querySelector('.vlog-player[data-youtube-id]');
        if (!container) return;

        var videoId = container.getAttribute('data-youtube-id');

        container.addEventListener('click', function () {
            if (container.classList.contains('is-playing')) return;
            container.classList.add('is-playing');

            // YouTube IFrame API 사용
            var iframeDiv = document.createElement('div');
            iframeDiv.id = 'vlog-yt-player';
            iframeDiv.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;';
            container.appendChild(iframeDiv);

            if (window.YT && window.YT.Player) {
                createPlayer(videoId);
            } else {
                // IFrame API 로드
                var tag = document.createElement('script');
                tag.src = 'https://www.youtube.com/iframe_api';
                document.head.appendChild(tag);
                window.onYouTubeIframeAPIReady = function () {
                    createPlayer(videoId);
                };
            }
        });
    }

    function createPlayer(videoId) {
        player = new YT.Player('vlog-yt-player', {
            videoId: videoId,
            playerVars: {
                autoplay: 1,
                rel: 0,
                modestbranding: 1,
                enablejsapi: 1
            },
            events: {
                onReady: function () {
                    playerReady = true;
                }
            }
        });
    }

    /**
     * 타임라인 클릭 → 영상 시간 이동
     */
    function initTimeline() {
        var timestamps = document.querySelectorAll('.vlog-timestamp[data-time]');
        if (!timestamps.length) return;

        timestamps.forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var seconds = parseTimestamp(el.getAttribute('data-time'));
                var container = document.querySelector('.vlog-player[data-youtube-id]');

                // 영상이 아직 로드되지 않은 경우 → 먼저 로드
                if (!container.classList.contains('is-playing')) {
                    container.click();
                    // 플레이어 준비 후 시간 이동
                    var checkInterval = setInterval(function () {
                        if (playerReady && player && player.seekTo) {
                            player.seekTo(seconds, true);
                            clearInterval(checkInterval);
                        }
                    }, 200);
                } else if (playerReady && player && player.seekTo) {
                    player.seekTo(seconds, true);
                }
            });
        });
    }

    /**
     * 영상 속 장소 지도 초기화
     */
    function initSpotsMap() {
        var mapEl = document.getElementById('ft-vlog-spots-map');
        if (!mapEl || typeof google === 'undefined') return;

        var data = window.ftVlogData || {};
        var spots = data.spots || [];
        if (!spots.length) return;

        // 좌표 중심 계산
        var bounds = new google.maps.LatLngBounds();
        spots.forEach(function (s) {
            if (s.lat && s.lng) {
                bounds.extend(new google.maps.LatLng(s.lat, s.lng));
            }
        });

        var map = new google.maps.Map(mapEl, {
            zoom: 13,
            center: bounds.getCenter(),
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
        });

        map.fitBounds(bounds);
        if (spots.length === 1) map.setZoom(15);

        var infoWindow = new google.maps.InfoWindow();

        spots.forEach(function (spot, index) {
            if (!spot.lat || !spot.lng) return;

            var marker = new google.maps.Marker({
                position: { lat: spot.lat, lng: spot.lng },
                map: map,
                title: spot.name,
                label: {
                    text: String(index + 1),
                    color: '#fff',
                    fontWeight: '600',
                    fontSize: '12px'
                }
            });

            var mapsUrl = 'https://www.google.com/maps/search/?api=1&query='
                        + encodeURIComponent(spot.name);

            var content = '<div style="max-width:220px;padding:4px;">'
                + '<strong style="font-size:14px;">' + spot.name + '</strong>';
            if (spot.description) {
                content += '<p style="font-size:12px;color:#666;margin:4px 0;">' + spot.description + '</p>';
            }
            content += '<a href="' + mapsUrl + '" target="_blank" rel="noopener" '
                + 'style="font-size:12px;color:#1a73e8;">'
                + (data.labels && data.labels.view_on_map || '구글맵에서 보기')
                + '</a></div>';

            marker.addListener('click', function () {
                infoWindow.setContent(content);
                infoWindow.open(map, marker);
            });
        });
    }

    // ── Init ──
    document.addEventListener('DOMContentLoaded', function () {
        initLazyEmbed();
        initTimeline();
        initSpotsMap();
    });
})();
