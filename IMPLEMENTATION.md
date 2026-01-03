# テキストパントリー サイト実装引き継ぎ書

## 現状 ✅ UI実装完了

Astro + Tailwind CSS v4 でサイトのUI実装が完了しました。

### 作成済みファイル

```
src/
├── content.config.ts          # Content Collections設定
├── content/
│   ├── staff/                 # スタッフMarkdown（3名）
│   └── works/                 # 作品Markdown（16作品）
├── styles/
│   └── global.css             # デザイントークン + ボタンスタイル
├── layouts/
│   └── BaseLayout.astro       # 基本レイアウト（OGP対応）
├── components/
│   ├── layout/
│   │   ├── Header.astro       # ヘッダー（モバイルメニュー付き）
│   │   ├── Footer.astro
│   │   ├── Navigation.astro
│   │   └── Container.astro
│   ├── ui/
│   │   ├── Button.astro       # ボタン（primary/secondary/ghost）
│   │   ├── SectionHeading.astro
│   │   ├── WorkCard.astro
│   │   └── StaffCard.astro
│   └── sections/
│       ├── Hero.astro
│       ├── About.astro
│       ├── FeaturedWorks.astro
│       ├── StaffPreview.astro
│       └── ContactCTA.astro
└── pages/
    ├── index.astro            # ホーム
    ├── staff/index.astro      # スタッフ一覧
    ├── works/
    │   ├── index.astro        # 仕事一覧（年別グループ）
    │   └── [slug].astro       # 作品詳細
    ├── contact/index.astro    # 問い合わせ（UIのみ）
    └── 404.astro

public/
├── favicon.svg
└── images/
    └── works/                 # 作品サムネイル（16枚）
```

---

## 起動方法

```bash
pnpm install   # 依存パッケージインストール
pnpm dev       # 開発サーバー起動
pnpm build     # 本番ビルド
pnpm preview   # ビルド結果プレビュー
```

---

## 技術的な注意点

### Tailwind CSS v4 のカスタムカラー問題

Tailwind CSS v4 の `@theme` で定義したカスタムカラー（`--color-foreground` 等）が、
ユーティリティクラス（`bg-foreground`）として正しく適用されない問題が発生しました。

**対処法**: ボタンのスタイルは `global.css` で直接CSSクラスとして定義しています。

```css
/* src/styles/global.css */
.btn-primary {
  background-color: #1a1a1a;
  color: #ffffff;
}
```

今後 Tailwind v4 が安定したら、ユーティリティクラスに移行可能です。

### Astro v5 Content Collections

Astro v5 では `render()` の呼び出し方が変更されています。

```astro
// 正しい書き方（v5）
import { getCollection, render } from "astro:content";
const { Content } = await render(work);

// 旧い書き方（v4）- 動作しない
const { Content } = await work.render();
```

---

## 残タスク

### 必須

1. **問い合わせフォーム送信機能**
   - reCAPTCHA 導入
   - PHP送信処理（`public/api/contact.php`）
   - `.htaccess` 設定

2. **スタッフ画像**
   - `/public/images/staff/aisoramanta.webp`
   - `/public/images/staff/kadotayuuichi.webp`
   - `/public/images/staff/kawakoshitakahiro.webp`
   - 推奨サイズ: 400x400px（正方形）

### 任意

- OG画像（`/public/og-image.jpg`）
- サイトマップ生成
- Google Analytics

---

## 開発ツール

Playwright でスクリーンショット確認ができます。

```bash
# 開発サーバー起動後
pnpm exec tsx screenshot.ts
```

---

## 参照ドキュメント

- **デザインガイドライン**: `CLAUDE.md`
- **Astro Content Collections**: https://docs.astro.build/en/guides/content-collections/
- **Tailwind CSS v4**: https://tailwindcss.com/docs
