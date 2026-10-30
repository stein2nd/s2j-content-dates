# S2J Content Dates - CHANGELOG

## unreleased

## 0.0.1 - 2026-10-30

### Changed

* 開発依存の `@s2j/docs-linter` を ^1.0.31、`rollup` を ^4.66.0、`sass` を ^1.107.0、`@typescript-eslint/*` を ^8.72.1、`@types/node` を ^26.6.6、`autoprefixer` を ^10.6.2に更新。
* `composer.lock` を Composer の lock メタデータ形式に合わせて更新。

## 0.0.1 - 2026-10-26

### Added

* プラグイン PHP の骨子 (`s2j-content-dates.php`、`includes/Plugin.php`、`uninstall.php`) と `vite.config.ts` のプレースホルダーを追加。

### Fixed

* `npm run lint:php` が PHPStan「No files found to analyse」で失敗する問題 — `includes/` に解析対象を置き、`phpstan.neon.dist` を更新した。

### Changed

* `phpcs.xml.dist` と `phpcs.wpcs.xml.dist` を、他 S2J プラグイン同型の PHPCS 設定に合わせた。
* `makejson` のデフォルトを purge ありにし、孤児 JED を残す `makejson:keep` (`--no-purge`) を追加した。

## 0.0.1 - 2026-10-24

### Fixed

* GitHub Actions「PHPUnit」が `phpunit:wp-env` 未定義で失敗する問題 — `npm run phpunit:wp-env` を追加し、WordPress 未起動のユニット PHPUnit (`phpunit.unit.xml.dist`) を CI で実行する。

### Added

* `composer.json` と `composer.lock` を追加し、PHPUnit、PHPStan、PHPCS、WPCS 向けの開発依存と lint スクリプトを置いた。`phpcs.xml.dist`、`phpcs.wpcs.xml.dist`、`phpstan.neon.dist`、`.env.tests.example` を同梱した。

### Changed

* WordPress 推奨要件に合わせ、実行環境の PHP 下限を v8.3にそろえた。`composer.json`、`phpcs.xml.dist`、`phpstan.neon.dist`、`.github/workflows/phpunit.yml`、`.github/workflows/dist-zip.yml`、`docs_mod/specs.md`、`README.md` を更新した。
* `.gitignore` に `vendor/`、テスト用 `.env.tests`、ローカル WordPress テスト用ディレクトリ、PHPUnit の結果キャッシュを追加した。
* `package.json` に PHP lint、i18n、ローカル PHPUnit 用の npm script を足した。`@s2j/docs-linter` を v1.0.30、`rollup` を v4.65.0、`vite` を v8.3.5に上げ、WordPress パッケージを v8– v11系に更新した。
* `.github/workflows/docs-lint.yml` で `composer.json` の変更時にもドキュメント lint を走らせる。

## 0.0.1 - 2026-10-15

### Changed

* 開発依存の `@s2j/docs-linter` を ^1.0.29、`@typescript-eslint/*` を ^8.71.2、`rollup` を ^4.64.7、`react` / `react-dom` を ^19.3.1に更新
* textlint の許可パターンに `#status-*` アンカー ID を追加

## 0.0.1 - 2026-10-12

### Added

* GitHub Actions に配布 zip ビルド (`dist-zip.yml`) と PHPUnit (`phpunit.yml`) を追加
* 配布 zip 作成スクリプト `tools/dist/make-dist-zip.mjs` を追加

## 0.0.1 - 2026-10-11

### Changed

* 開発依存の `@wordpress/*`、`vite` ^8.3.4、`rollup` ^4.64.5、`@typescript-eslint/*` ^8.71.1、`@types/node` ^26.6.5を更新
* 開発依存の `braces` v3.0.3 (GHSA-vfj7-8cjw-p6xm) は修正版が未公開のため、深刻度 high の指摘7件 (CVE-2026-93687) は残す。`@s2j/docs-linter` 経由の深刻度 moderate の指摘10件も修正版待ちのため残す

## 0.0.1 - 2026-10-10

### Changed

* 仕様ドラフトの「単体表示へ更新日」を「単体表示に更新日」に直し、ドキュメント lint の表記に合わせる。

## 0.0.1 - 2026-10-08

### Changed

* 仕様ドラフトの「とき」と「次」を「場合」と「下記」に直し、ドキュメント lint の表記に合わせる。

## 0.0.1 - 2026-10-05

### Changed

* Query Loop の更新日ソートは、初期値では出さない。サイトの `s2j_content_dates_query_loop` (`off` または `all`) と、タイプごとの `query_loop` で出し分ける。
* 集約する子は `post_parent` の投稿だけにする。投稿メタや関連フィールドで結ぶ組は集めない。
* `show_ui` だけで `public` ではない CPT も、設定の表に出す。
* Composer では `s2j/content-dates-service` を Packagist のパッケージ名だけで require する。
* アンインストールで `s2j_content_dates_query_loop` も消す。

## 0.0.1 - 2026-10-04

### Added

* プラグイン仕様ドラフトを `docs_mod/specs.md` に記載
* npm v12以降向けに `.npmrc` の `allow-git=all` と `package.json` の `allowScripts` を追加

### Changed

* 開発依存の `braces` v3.0.3 (GHSA-vfj7-8cjw-p6xm: 深くネストしたパターンで Node.js プロセスが終了する) は修正版が未公開のため、深刻度 high の指摘7件 (CVE-2026-93687) は残す。
