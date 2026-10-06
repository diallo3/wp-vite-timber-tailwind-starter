// Read/write acf-json files in the same format ACF exports (4-space indent, escaped slashes).
const fs = require("fs");
const path = require("path");

const dir = path.join(__dirname, "..", "acf-json");

const read = (name) => JSON.parse(fs.readFileSync(path.join(dir, name), "utf8"));

const write = (name, data, { touch = true } = {}) => {
    if (touch) data.modified = Math.floor(Date.now() / 1000);
    const json = JSON.stringify(data, null, 4).replace(/\//g, "\\/");
    fs.writeFileSync(path.join(dir, name), json + "\n");
};

const groups = () => fs.readdirSync(dir).filter((f) => f.startsWith("group_") && f.endsWith(".json"));

module.exports = { dir, read, write, groups };
