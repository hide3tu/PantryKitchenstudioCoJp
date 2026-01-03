# テキストパントリー サイト実装引き継ぎ書

## 現状

Astro + Tailwind CSS v4 でサイトのUI実装が完了しました。

---

## 残タスク

### 必須

1. **問い合わせフォーム送信機能**
   - reCAPTCHA 導入
   - PHP送信処理（`public/api/contact.php`）
   - `.htaccess` 設定

### 任意

- OG画像（`/public/og-image.jpg`）
- サイトマップ生成
- Google Analytics

---

## 起動方法

```bash
pnpm install   # 依存パッケージインストール
pnpm dev       # 開発サーバー起動（localhost:4324）
pnpm build     # 本番ビルド
pnpm preview   # ビルド結果プレビュー
```

---

## 技術的な注意点

### Tailwind CSS v4 のカスタムカラー問題

Tailwind CSS v4 の `@theme` で定義したカスタムカラーが、ユーティリティクラスとして正しく適用されない問題があったため、ボタンのスタイルは `global.css` で直接CSSクラスとして定義しています。

### Astro v5 Content Collections

```astro
// 正しい書き方（v5）
import { getCollection, render } from "astro:content";
const { Content } = await render(work);
```

---

## 参照ドキュメント

- **デザインガイドライン**: `CLAUDE.md`
- **Astro Content Collections**: https://docs.astro.build/en/guides/content-collections/
- **Tailwind CSS v4**: https://tailwindcss.com/docs
