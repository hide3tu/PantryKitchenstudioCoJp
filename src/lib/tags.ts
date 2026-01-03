import { getCollection } from "astro:content";

export async function getAllTags(): Promise<{ tag: string; count: number }[]> {
  const works = await getCollection("works");
  const tagCounts = new Map<string, number>();

  works.forEach((work) => {
    work.data.tags?.forEach((tag) => {
      if (tag.trim()) {
        tagCounts.set(tag, (tagCounts.get(tag) || 0) + 1);
      }
    });
  });

  return Array.from(tagCounts.entries())
    .map(([tag, count]) => ({ tag, count }))
    .sort((a, b) => b.count - a.count);
}
