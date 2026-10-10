# S2J Content Dates - CHANGELOG

## unreleased

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
