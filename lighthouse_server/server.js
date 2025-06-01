const express = require('express');
const cors = require('cors');
const { exec } = require('child_process');

const app = express();
app.use(cors());
app.use(express.json());

app.get('/', (req, res) => {
    res.send('Lighthouse audit server is running.');
});

app.get('/audit', (req, res) => {
    res.send('Use POST /audit with a JSON body like {"url": "https://example.com"}');
});

app.post('/audit', (req, res) => {
    const url = req.body.url;
    if (!url) return res.status(400).json({ error: 'URL is required' });

    exec(`lighthouse ${url} --quiet --chrome-flags="--headless" --output=json --output-path=stdout`, (error, stdout, stderr) => {
        if (error) {
            console.error('Lighthouse error:', error);
            return res.status(500).json({ error: 'Lighthouse audit failed' });
        }

        try {
            const report = JSON.parse(stdout);
            const score = report.categories.performance.score * 100;
            res.json({ performance: score });
        } catch (e) {
            console.error('JSON parse error:', e);
            res.status(500).json({ error: 'Failed to parse Lighthouse report' });
        }
    });
});

app.listen(3000, () => {
    console.log('Performance audit server running on http://localhost:3000');
});
