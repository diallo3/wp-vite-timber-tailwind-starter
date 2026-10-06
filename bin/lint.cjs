// `npm run lint`: PHP syntax, acf-json integrity and Twig templates.
const { execFileSync } = require("child_process");
const fs = require("fs");
const path = require("path");
const acf = require("./acf-json.cjs");

const root = path.join(__dirname, "..");
const skip = new Set(["node_modules", "vendor", "dist", ".git", "wordpress"]);
let failed = false;

const walk = (dir, ext, out = []) => {
    for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
        if (skip.has(entry.name)) continue;
        const full = path.join(dir, entry.name);
        if (entry.isDirectory()) walk(full, ext, out);
        else if (entry.name.endsWith(ext)) out.push(full);
    }
    return out;
};

// PHP syntax
const phpFiles = walk(root, ".php");
for (const file of phpFiles) {
    try {
        execFileSync("php", ["-l", file], { stdio: "pipe" });
    } catch (e) {
        failed = true;
        process.stdout.write(e.stdout.toString() + e.stderr.toString());
    }
}
console.log(`php: ${phpFiles.length} files checked`);

// acf-json: valid JSON, unique keys, component and conditional references resolve
const files = fs.readdirSync(acf.dir).filter((f) => f.endsWith(".json"));
const groupKeys = new Set();
const fieldKeys = new Map();
const problems = [];

const groups = files.map((file) => {
    try {
        return [file, acf.read(file)];
    } catch (e) {
        problems.push(`${file}: invalid JSON (${e.message})`);
        return [file, null];
    }
});

const eachField = (fields, fn) => (fields || []).forEach((f) => {
    fn(f);
    eachField(f.sub_fields, fn);
    Object.values(f.layouts || {}).forEach((layout) => eachField(layout.sub_fields, fn));
});

for (const [file, group] of groups) {
    if (!group || !file.startsWith("group_")) continue;
    if (groupKeys.has(group.key)) problems.push(`${file}: duplicate group key ${group.key}`);
    groupKeys.add(group.key);
    eachField(group.fields, (f) => {
        if (fieldKeys.has(f.key)) problems.push(`${file}: field key ${f.key} also used in ${fieldKeys.get(f.key)}`);
        fieldKeys.set(f.key, file);
    });
}

for (const [file, group] of groups) {
    if (!group || !file.startsWith("group_")) continue;
    eachField(group.fields, (f) => {
        if (f.type === "component_field" && !groupKeys.has(f.field_group_key)) {
            problems.push(`${file}: "${f.name}" uses missing component ${f.field_group_key}`);
        }
        for (const rule of Array.isArray(f.conditional_logic) ? f.conditional_logic.flat() : []) {
            if (!fieldKeys.has(rule.field)) problems.push(`${file}: "${f.name}" condition points at missing field ${rule.field}`);
        }
    });
}

problems.forEach((p) => console.log(p));
console.log(problems.length ? `acf-json: ${problems.length} problem(s)` : `acf-json: ${files.length} files ok`);
failed ||= problems.length > 0;

// Twig
if (fs.existsSync(path.join(root, "vendor/autoload.php"))) {
    try {
        process.stdout.write(execFileSync("php", [path.join(__dirname, "twig-lint.php")]).toString());
    } catch (e) {
        failed = true;
        process.stdout.write(e.stdout.toString());
    }
} else {
    console.log("twig: skipped (run composer install)");
}

process.exit(failed ? 1 : 0);
