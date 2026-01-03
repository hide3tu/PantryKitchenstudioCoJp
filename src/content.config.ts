import { defineCollection, z } from "astro:content";
import { glob } from "astro/loaders";

// スタッフコレクション
const staffCollection = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/staff" }),
  schema: z.object({
    name: z.string(),
    nameEn: z.string().optional(),
    role: z.string(),
    bio: z.string(),
    image: z.string(),
    order: z.number().default(0),
    featured: z.boolean().default(false),
    skills: z.array(z.string()).optional(),
    social: z
      .object({
        twitter: z.string().optional(),
        website: z.string().optional(),
      })
      .optional(),
  }),
});

// 作品コレクション
const worksCollection = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/works" }),
  schema: z.object({
    title: z.string(),
    titleEn: z.string().optional(),
    client: z.string().optional(),
    category: z.enum([
      "novel",
      "game",
      "anime",
      "manga",
      "drama-cd",
      "other",
    ]),
    year: z.number(),
    publishDate: z.coerce.date(),
    thumbnail: z.string().optional(),
    images: z.array(z.string()).optional(),
    description: z.string(),
    staff: z.array(z.string()).optional(),
    featured: z.boolean().default(false),
    order: z.number().default(0),
    externalUrl: z.string().optional(),
    tags: z.array(z.string()).optional(),
  }),
});

export const collections = {
  staff: staffCollection,
  works: worksCollection,
};
