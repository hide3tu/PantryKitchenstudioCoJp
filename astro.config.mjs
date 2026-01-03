import { defineConfig } from "astro/config";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  site: "https://pantry.kitchenstudio.co.jp",
  output: "static",
  vite: {
    plugins: [tailwindcss()],
  },
});
