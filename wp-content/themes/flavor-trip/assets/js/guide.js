/**
 * Destination Guide — 탭 전환 + 테이블 정렬
 *
 * @package Flavor_Trip
 */
(function () {
    'use strict';

    // ── 탭 전환 ──
    var tabs = document.querySelectorAll('.guide-tab');
    var panels = document.querySelectorAll('.guide-table-panel');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var target = this.getAttribute('data-tab');

            tabs.forEach(function (t) { t.classList.remove('active'); });
            panels.forEach(function (p) { p.classList.remove('active'); });

            this.classList.add('active');
            var panel = document.getElementById('panel-' + target);
            if (panel) panel.classList.add('active');
        });
    });

    // ── 테이블 정렬 ──
    document.querySelectorAll('.guide-table th[data-sort-key]').forEach(function (th) {
        th.addEventListener('click', function () {
            var table = this.closest('table');
            var tbody = table.querySelector('tbody');
            var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
            var key = this.getAttribute('data-sort-key');
            var colIndex = Array.prototype.indexOf.call(this.parentNode.children, this);

            // 정렬 방향 토글
            var isAsc = this.classList.contains('sort-asc');

            // 같은 테이블의 모든 th에서 정렬 클래스 제거
            table.querySelectorAll('th').forEach(function (h) {
                h.classList.remove('sort-asc', 'sort-desc');
                var icon = h.querySelector('.sort-icon');
                if (icon) icon.textContent = '⇅';
            });

            var direction = isAsc ? 'desc' : 'asc';
            this.classList.add('sort-' + direction);
            var icon = this.querySelector('.sort-icon');
            if (icon) icon.textContent = direction === 'asc' ? '▲' : '▼';

            rows.sort(function (a, b) {
                var cellA = a.children[colIndex];
                var cellB = b.children[colIndex];
                var valA = cellA ? (cellA.getAttribute('data-value') || cellA.textContent.trim()) : '';
                var valB = cellB ? (cellB.getAttribute('data-value') || cellB.textContent.trim()) : '';

                // 숫자 정렬
                var numA = parseFloat(valA);
                var numB = parseFloat(valB);
                if (!isNaN(numA) && !isNaN(numB)) {
                    return direction === 'asc' ? numA - numB : numB - numA;
                }

                // 텍스트 정렬
                return direction === 'asc'
                    ? valA.localeCompare(valB, 'ko')
                    : valB.localeCompare(valA, 'ko');
            });

            // 재번호
            rows.forEach(function (row, i) {
                tbody.appendChild(row);
                var numCell = row.querySelector('.col-num');
                if (numCell) numCell.textContent = i + 1;
            });
        });
    });
})();
