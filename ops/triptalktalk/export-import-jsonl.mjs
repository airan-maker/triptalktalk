import fs from 'node:fs';
import path from 'node:path';

const input = process.env.INPUT_JSONL || './output/travel-data.jsonl';
const output = process.env.OUTPUT_JSONL || './data/vlog-import/vlogs.jsonl';

if (!fs.existsSync(input)) {
  console.error(`Input JSONL not found: ${input}`);
  process.exit(1);
}

const lines = fs
  .readFileSync(input, 'utf8')
  .split(/\r?\n/)
  .map((x) => x.trim())
  .filter(Boolean);

const out = [];
for (const line of lines) {
  try {
    const row = JSON.parse(line);
    const source = row.source || {};
    const draft = row.vlogDraft || {};
    if (!source.videoUrl && !source.youtubeId) continue;

    out.push(
      JSON.stringify({
        source: {
          videoUrl: source.videoUrl || null,
          youtubeId: source.youtubeId || null,
          videoTitle: source.videoTitle || null,
          channelName: source.channelName || null,
          channelUrl: source.channelUrl || null,
          duration: source.duration || null
        },
        summary: row.summary || '',
        timeline: Array.isArray(row.timeline) ? row.timeline : [],
        places: Array.isArray(row.places) ? row.places : [],
        practicalIntel: row.practicalIntel || {},
        vlogDraft: {
          title: draft.title || source.videoTitle || '브이로그 요약',
          excerpt: draft.excerpt || row.summary || '',
          timeline: Array.isArray(draft.timeline) ? draft.timeline : [],
          spots: Array.isArray(draft.spots) ? draft.spots : []
        }
      })
    );
  } catch {
    // skip malformed lines
  }
}

fs.mkdirSync(path.dirname(output), { recursive: true });
fs.writeFileSync(output, `${out.join('\n')}\n`, 'utf8');
console.log(`Exported ${out.length} rows -> ${output}`);
