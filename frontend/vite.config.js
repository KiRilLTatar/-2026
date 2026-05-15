import { env } from "node:process";
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

const backendUrl = env.BACKEND_URL || "http://backend";

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      "/api": {
        target: backendUrl,
        changeOrigin: true,
      },
    },
  },
});
