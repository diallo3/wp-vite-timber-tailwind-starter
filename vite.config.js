import fs from "node:fs";
import path from "node:path";
import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";
import mkcert from "vite-plugin-mkcert";
import ViteRestart from "vite-plugin-restart";
import "dotenv/config";

const {
  VITE_OUTPUT_DIR = "dist",
  VITE_ENTRY_POINT = "src/main.js",
  VITE_PROTOCOL = "https",
  VITE_HOST = "localhost",
  VITE_PORT = "3000",
} = process.env;

const devServerUrl = `${VITE_PROTOCOL}://${VITE_HOST}:${VITE_PORT}`;

/**
 * Writes the dev server URL to `.vite-hot` while `vite` is running.
 * lib/functions/lib-vite.php checks for this file instead of probing the server.
 */
function hotFile() {
  const file = path.resolve(".vite-hot");
  const remove = () => fs.rmSync(file, { force: true });

  return {
    name: "theme-hot-file",
    apply: "serve",
    configureServer(server) {
      server.httpServer?.once("listening", () => fs.writeFileSync(file, devServerUrl));
      server.httpServer?.once("close", remove);
      for (const signal of ["SIGINT", "SIGTERM", "SIGHUP"]) {
        process.once(signal, () => {
          remove();
          process.exit();
        });
      }
      process.once("exit", remove);
    },
  };
}

export default defineConfig(({ command }) => ({
  base: command === "serve" ? "/" : `/${VITE_OUTPUT_DIR}/`,
  // public/ is served by WordPress directly; nothing needs copying into dist/.
  publicDir: false,
  build: {
    manifest: true,
    outDir: VITE_OUTPUT_DIR,
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: VITE_ENTRY_POINT,
        admin: "src/admin.css",
        preview: "src/preview.js",
      },
    },
  },
  plugins: [
    tailwindcss(),
    mkcert(),
    hotFile(),
    ViteRestart({
      reload: ["./**/*.twig", "./**/*.php", "!vendor/**/*", "!node_modules/**/*"],
    }),
  ],
  server: {
    https: VITE_PROTOCOL === "https",
    cors: true,
    fs: {
      strict: false,
    },
    origin: devServerUrl,
    port: parseInt(VITE_PORT, 10),
    strictPort: true,
    hmr: {
      host: VITE_HOST,
    },
    host: true,
  },
}));
