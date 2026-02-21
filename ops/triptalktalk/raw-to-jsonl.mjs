#!/usr/bin/env node
/**
 * raw-to-jsonl.mjs
 *
 * Parses Vlog_Raw/raw/*.txt (Gemini-generated timeline text) into
 * data/vlog-import/vlogs.jsonl for WP-CLI import.
 *
 * Usage:
 *   node ops/triptalktalk/raw-to-jsonl.mjs
 *   INPUT_DIR=./other/raw OUTPUT_JSONL=./out.jsonl node ops/triptalktalk/raw-to-jsonl.mjs
 */

import fs from 'node:fs';
import path from 'node:path';

/* ------------------------------------------------------------------ */
/*  Configuration                                                      */
/* ------------------------------------------------------------------ */

const INPUT_DIR = process.env.INPUT_DIR || './Vlog_Raw/raw';
const OUTPUT_JSONL = process.env.OUTPUT_JSONL || './data/vlog-import/vlogs.jsonl';

/* ------------------------------------------------------------------ */
/*  City detection mapping                                             */
/* ------------------------------------------------------------------ */

const CITY_MAP = {
  // Japan
  '교토': { slug: 'kyoto', parent: 'japan', label: '교토' },
  'Kyoto': { slug: 'kyoto', parent: 'japan', label: '교토' },
  '도쿄': { slug: 'tokyo', parent: 'japan', label: '도쿄' },
  'Tokyo': { slug: 'tokyo', parent: 'japan', label: '도쿄' },
  '오사카': { slug: 'osaka', parent: 'japan', label: '오사카' },
  'Osaka': { slug: 'osaka', parent: 'japan', label: '오사카' },
  '삿포로': { slug: 'sapporo', parent: 'japan', label: '삿포로' },
  'Sapporo': { slug: 'sapporo', parent: 'japan', label: '삿포로' },
  '후쿠오카': { slug: 'fukuoka', parent: 'japan', label: '후쿠오카' },
  'Fukuoka': { slug: 'fukuoka', parent: 'japan', label: '후쿠오카' },
  '히로시마': { slug: 'hiroshima', parent: 'japan', label: '히로시마' },
  'Hiroshima': { slug: 'hiroshima', parent: 'japan', label: '히로시마' },
  '가나자와': { slug: 'kanazawa', parent: 'japan', label: '가나자와' },
  'Kanazawa': { slug: 'kanazawa', parent: 'japan', label: '가나자와' },
  '나라': { slug: 'nara', parent: 'japan', label: '나라' },
  'Nara': { slug: 'nara', parent: 'japan', label: '나라' },
  '나고야': { slug: 'nagoya', parent: 'japan', label: '나고야' },
  'Nagoya': { slug: 'nagoya', parent: 'japan', label: '나고야' },
  '요코하마': { slug: 'yokohama', parent: 'japan', label: '요코하마' },
  '고베': { slug: 'kobe', parent: 'japan', label: '고베' },
  'Kobe': { slug: 'kobe', parent: 'japan', label: '고베' },
  '오키나와': { slug: 'okinawa', parent: 'japan', label: '오키나와' },
  'Okinawa': { slug: 'okinawa', parent: 'japan', label: '오키나와' },
  '아카메': { slug: 'osaka', parent: 'japan', label: '오사카' }, // near Osaka
  '미에': { slug: 'mie', parent: 'japan', label: '미에' },
  '우지': { slug: 'kyoto', parent: 'japan', label: '교토' }, // Uji is in Kyoto pref

  // Korea
  '서울': { slug: 'seoul', parent: 'korea', label: '서울' },
  'Seoul': { slug: 'seoul', parent: 'korea', label: '서울' },
  '제주': { slug: 'jeju', parent: 'korea', label: '제주' },
  'Jeju': { slug: 'jeju', parent: 'korea', label: '제주' },
  '부산': { slug: 'busan', parent: 'korea', label: '부산' },
  'Busan': { slug: 'busan', parent: 'korea', label: '부산' },

  // Southeast Asia
  '방콕': { slug: 'bangkok', parent: 'thailand', label: '방콕' },
  'Bangkok': { slug: 'bangkok', parent: 'thailand', label: '방콕' },
  '나트랑': { slug: 'nhatrang', parent: 'vietnam', label: '나트랑' },
  'Nha Trang': { slug: 'nhatrang', parent: 'vietnam', label: '나트랑' },
  '다낭': { slug: 'danang', parent: 'vietnam', label: '다낭' },
  'Da Nang': { slug: 'danang', parent: 'vietnam', label: '다낭' },
  '호이안': { slug: 'hoian', parent: 'vietnam', label: '호이안' },

  // Others
  '파리': { slug: 'paris', parent: 'france', label: '파리' },
  'Paris': { slug: 'paris', parent: 'france', label: '파리' },
  '하와이': { slug: 'hawaii', parent: 'usa', label: '하와이' },
  'Hawaii': { slug: 'hawaii', parent: 'usa', label: '하와이' },
  '타이베이': { slug: 'taipei', parent: 'taiwan', label: '타이베이' },
  'Taipei': { slug: 'taipei', parent: 'taiwan', label: '타이베이' },

  // Additional Japan cities
  '시즈오카': { slug: 'shizuoka', parent: 'japan', label: '시즈오카' },
  'Shizuoka': { slug: 'shizuoka', parent: 'japan', label: '시즈오카' },
  '가와구치': { slug: 'kawaguchiko', parent: 'japan', label: '가와구치코' },
  '후지산': { slug: 'fujisan', parent: 'japan', label: '후지산' },
  '시가': { slug: 'shiga', parent: 'japan', label: '시가' },
  '마이바라': { slug: 'shiga', parent: 'japan', label: '시가' },
  '군조': { slug: 'gujo', parent: 'japan', label: '군조' },
  'Gujo': { slug: 'gujo', parent: 'japan', label: '군조' },

  // Additional Southeast Asia
  '호치민': { slug: 'hochiminh', parent: 'vietnam', label: '호치민' },
  'Ho Chi Minh': { slug: 'hochiminh', parent: 'vietnam', label: '호치민' },

  // China
  '상하이': { slug: 'shanghai', parent: 'china', label: '상하이' },
  'Shanghai': { slug: 'shanghai', parent: 'china', label: '상하이' },
};

/* ------------------------------------------------------------------ */
/*  Travel style keyword mapping                                       */
/* ------------------------------------------------------------------ */

const STYLE_KEYWORDS = {
  '미식여행': ['카페', '커피', '맛집', '음식', '라멘', '스시', '레스토랑', '식당', '디저트', '빵집', '베이커리', '런치', '브런치', '아침 식사', '점심', '저녁', '맥주', '사케', '이자카야', '우동', '소바', '카레', '푸딩'],
  '문화탐방': ['사찰', '신사', '절', '사원', '역사', '문화', '박물관', '미술관', '전시', '성', '궁', '유적', '문화유산', '도리이'],
  '쇼핑': ['쇼핑', '시장', '빈티지', '상점가', '쇼텐가이', '아케이드', '몰'],
  '힐링여행': ['온천', '힐링', '산책', '정원', '공원', '강변', '숲', '폭포', '해변', '바다', '호수', '자연'],
  '도시여행': ['역', '거리', '동네', '투어', '탐방', '야경', '전망', '타워'],
};

/* ------------------------------------------------------------------ */
/*  Gemini prefix/suffix stripping                                     */
/* ------------------------------------------------------------------ */

/**
 * Strip Gemini UI header (lines up to and including the Korean prompt)
 * and footer (follow-up questions + disclaimer).
 */
function stripGeminiWrapper(text) {
  const lines = text.split(/\r?\n/);

  // Find end of header: line containing the Korean prompt
  let headerEnd = -1;
  for (let i = 0; i < Math.min(lines.length, 20); i++) {
    if (lines[i].includes('자세히 설명해줘')) {
      headerEnd = i;
      break;
    }
  }

  // Find start of footer: "AI can make mistakes" line
  let footerStart = lines.length;
  for (let i = lines.length - 1; i >= 0; i--) {
    if (lines[i].includes('AI can make mistakes') || lines[i].includes('Made with Gemini')) {
      footerStart = i;
    }
  }

  // Also remove follow-up question lines before the footer
  // These are short question-like lines (ending with ?) just before footer
  while (footerStart > 0 && lines[footerStart - 1].trim().endsWith('?')) {
    footerStart--;
  }
  // Remove empty lines before questions
  while (footerStart > 0 && lines[footerStart - 1].trim() === '') {
    footerStart--;
  }

  const bodyLines = lines.slice(headerEnd + 1, footerStart);

  // Trim leading/trailing empty lines
  let start = 0;
  while (start < bodyLines.length && bodyLines[start].trim() === '') start++;
  let end = bodyLines.length;
  while (end > start && bodyLines[end - 1].trim() === '') end--;

  return bodyLines.slice(start, end).join('\n');
}

/* ------------------------------------------------------------------ */
/*  Timeline parsing                                                   */
/* ------------------------------------------------------------------ */

/**
 * Parse timeline entries from body text.
 *
 * Patterns supported:
 *   장소명 (H:MM-H:MM): 설명...
 *   장소명 (H:MM): 설명...
 *   장소명: (H:MM) 설명...
 *   (H:MM) 장소명: 설명...
 */
function parseTimeline(body) {
  const lines = body.split('\n');

  // Regex patterns for timeline headers
  // Pattern 1: "장소명 (시간-시간):" or "장소명 (시간):" — colon after parens
  const pattern1 = /^(.+?)\s*\((\d{1,2}:\d{2}(?:\s*-\s*\d{1,2}:\d{2})?)\)\s*[:：]\s*(.*)/;
  // Pattern 1b: "장소명 (시간-시간)" — NO colon, line ends after parens (or has trailing whitespace)
  const pattern1b = /^(.{4,}?)\s*\((\d{1,2}:\d{2}(?:\s*-\s*\d{1,2}:\d{2})?)\)\s*$/;
  // Pattern 2: "(시간) 장소명:" — time at start
  const pattern2 = /^\((\d{1,2}:\d{2}(?:\s*-\s*\d{1,2}:\d{2})?)\)\s*(.+?)\s*[:：]\s*(.*)/;
  // Pattern 3: "장소명: (시간) 설명" — time after colon
  const pattern3 = /^(.+?)\s*[:：]\s*\((\d{1,2}:\d{2}(?:\s*-\s*\d{1,2}:\d{2})?)\)\s*(.*)/;
  // Day headers to skip
  const dayPattern = /^(?:(?:첫째|둘째|셋째|넷째|다섯째)\s*날|Day\s*\d+)\s*[:：]?\s*$/i;

  // Pass 1: find all timeline header line indices
  const headers = [];
  for (let i = 0; i < lines.length; i++) {
    const trimmed = lines[i].trim();
    if (!trimmed || dayPattern.test(trimmed)) continue;

    let match = trimmed.match(pattern1) || trimmed.match(pattern3);
    if (match) {
      headers.push({ idx: i, title: cleanTitle(match[1]), time: normalizeTime(match[2]), inlineDesc: match[3].trim() });
      continue;
    }
    match = trimmed.match(pattern1b);
    if (match) {
      headers.push({ idx: i, title: cleanTitle(match[1]), time: normalizeTime(match[2]), inlineDesc: '' });
      continue;
    }
    match = trimmed.match(pattern2);
    if (match) {
      headers.push({ idx: i, title: cleanTitle(match[2]), time: normalizeTime(match[1]), inlineDesc: match[3].trim() });
    }
  }

  // Pass 2: for each header, collect description from lines until next header
  const timeline = [];
  for (let h = 0; h < headers.length; h++) {
    const { idx, title, time, inlineDesc } = headers[h];
    const nextIdx = h + 1 < headers.length ? headers[h + 1].idx : lines.length;

    // Gather description lines between this header and next
    const descParts = [];
    if (inlineDesc) descParts.push(inlineDesc);

    for (let j = idx + 1; j < nextIdx; j++) {
      const trimmed = lines[j].trim();
      if (!trimmed) continue;
      if (dayPattern.test(trimmed)) continue;
      // Skip inline timestamps like "(1:23)" at start of continuation lines
      const cleaned = trimmed.replace(/^\(\d{1,2}:\d{2}(?:\s*-\s*\d{1,2}:\d{2})?\)\s*/, '');
      if (cleaned) descParts.push(cleaned);
    }

    const description = descParts.join(' ').trim();
    // Limit description length to keep JSONL manageable
    timeline.push({
      time,
      title,
      description: description.length > 500 ? description.slice(0, 497) + '...' : description,
    });
  }

  return timeline;
}

/**
 * Clean up title text - remove markdown bold markers, trim
 */
function cleanTitle(title) {
  return title.replace(/\*\*/g, '').replace(/^[-•]\s*/, '').trim();
}

/**
 * Normalize time string: "1:23-4:43" → "1:23"
 * We keep only the start time for the timeline marker.
 */
function normalizeTime(timeStr) {
  const parts = timeStr.split('-');
  return parts[0].trim();
}

/* ------------------------------------------------------------------ */
/*  Destination detection                                              */
/* ------------------------------------------------------------------ */

function detectDestinations(body) {
  const slugs = new Set();
  const parents = new Set();

  for (const [keyword, info] of Object.entries(CITY_MAP)) {
    if (body.includes(keyword)) {
      slugs.add(info.slug);
      parents.add(info.parent);
    }
  }

  // Combine: parent slugs first, then city slugs
  const result = [];
  for (const p of parents) result.push(p);
  for (const s of slugs) {
    if (!parents.has(s)) result.push(s);
  }

  return result;
}

/* ------------------------------------------------------------------ */
/*  Travel style detection                                             */
/* ------------------------------------------------------------------ */

function detectTravelStyles(body) {
  const styles = [];

  for (const [style, keywords] of Object.entries(STYLE_KEYWORDS)) {
    let matchCount = 0;
    for (const kw of keywords) {
      if (body.includes(kw)) matchCount++;
    }
    // Require at least 2 keyword matches to assign a style
    if (matchCount >= 2) {
      styles.push(style);
    }
  }

  // Default to 도시여행 if nothing matched
  if (styles.length === 0) {
    styles.push('도시여행');
  }

  return styles;
}

/* ------------------------------------------------------------------ */
/*  Title generation                                                   */
/* ------------------------------------------------------------------ */

function generateTitle(body, destinations, styles, timeline) {
  // Find the city label from detected destinations
  let cityLabel = '';
  for (const [keyword, info] of Object.entries(CITY_MAP)) {
    if (destinations.includes(info.slug) && info.parent !== info.slug) {
      cityLabel = info.label;
      break;
    }
  }

  if (!cityLabel) {
    // Fallback: extract a short title from the first meaningful line
    const firstLine = body.split('\n').find(l => l.trim().length > 10) || '';
    // Take first sentence or first 40 chars
    const sentence = firstLine.split(/[.。!！]/)[0].trim();
    return (sentence.length > 40 ? sentence.slice(0, 37) + '...' : sentence) || '브이로그';
  }

  // Count timeline entries to infer duration
  const spotCount = timeline.length;

  // Build descriptive suffix from styles
  const styleLabels = [];
  if (styles.includes('미식여행')) styleLabels.push('맛집');
  if (styles.includes('문화탐방')) styleLabels.push('문화');
  if (styles.includes('힐링여행')) styleLabels.push('힐링');
  if (styles.includes('쇼핑')) styleLabels.push('쇼핑');

  // Check for day markers to estimate trip duration
  const dayCount = (body.match(/(첫째|둘째|셋째|넷째|다섯째)\s*날/g) || []).length;
  const dayLabel = dayCount > 1 ? `${dayCount}일` : '당일';

  const suffix = styleLabels.length > 0
    ? styleLabels.join('·')
    : `${spotCount}곳`;

  return `${cityLabel} ${dayLabel} ${suffix} 투어`;
}

/* ------------------------------------------------------------------ */
/*  Excerpt generation                                                 */
/* ------------------------------------------------------------------ */

function generateExcerpt(body) {
  // Take first meaningful paragraph, max 200 chars
  const paragraphs = body.split(/\n\n+/);
  for (const p of paragraphs) {
    const clean = p.replace(/\n/g, ' ').trim();
    if (clean.length > 20) {
      return clean.length > 200 ? clean.slice(0, 197) + '...' : clean;
    }
  }
  return body.slice(0, 200).trim();
}

/* ------------------------------------------------------------------ */
/*  Main processing                                                    */
/* ------------------------------------------------------------------ */

function processFile(filePath) {
  const youtubeId = path.basename(filePath, '.txt');
  const raw = fs.readFileSync(filePath, 'utf8');
  const body = stripGeminiWrapper(raw);

  if (!body || body.trim().length < 30) {
    console.warn(`  SKIP ${youtubeId}: body too short after stripping`);
    return null;
  }

  const timeline = parseTimeline(body);
  const destinations = detectDestinations(body);
  const travelStyles = detectTravelStyles(body);
  const title = generateTitle(body, destinations, travelStyles, timeline);
  const excerpt = generateExcerpt(body);

  return {
    source: { youtubeId },
    vlogDraft: {
      title,
      excerpt,
      destination: destinations,
      travelStyle: travelStyles,
      timeline,
      spots: [],
    },
  };
}

function main() {
  if (!fs.existsSync(INPUT_DIR)) {
    console.error(`Input directory not found: ${INPUT_DIR}`);
    process.exit(1);
  }

  const files = fs.readdirSync(INPUT_DIR)
    .filter(f => f.endsWith('.txt') && !f.startsWith('._'))
    .sort();

  console.log(`Found ${files.length} raw files in ${INPUT_DIR}`);

  const results = [];
  let skipped = 0;

  for (const file of files) {
    const filePath = path.join(INPUT_DIR, file);
    const result = processFile(filePath);
    if (result) {
      results.push(result);
    } else {
      skipped++;
    }
  }

  // Write output
  fs.mkdirSync(path.dirname(OUTPUT_JSONL), { recursive: true });
  const output = results.map(r => JSON.stringify(r)).join('\n') + '\n';
  fs.writeFileSync(OUTPUT_JSONL, output, 'utf8');

  console.log(`\nDone: ${results.length} exported, ${skipped} skipped -> ${OUTPUT_JSONL}`);

  // Summary stats
  const destCounts = {};
  const styleCounts = {};
  for (const r of results) {
    for (const d of r.vlogDraft.destination) {
      destCounts[d] = (destCounts[d] || 0) + 1;
    }
    for (const s of r.vlogDraft.travelStyle) {
      styleCounts[s] = (styleCounts[s] || 0) + 1;
    }
  }
  console.log('\nDestinations:', JSON.stringify(destCounts, null, 2));
  console.log('Styles:', JSON.stringify(styleCounts, null, 2));
}

main();
