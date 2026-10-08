# S2J Content Dates - プラグイン仕様 (ドラフト)

採用・合意前の設計メモとして `docs_mod/` に置き、確定後は `docs/SPEC.md` に移行します。状態はドラフトです。記録日は2026-10-04です。

計算の正は [S2J Content Dates Service のサービス仕様](https://github.com/stein2nd/s2j-content-dates-service/blob/main/docs_mod/service_spec.md) です。本書は、WordPress 側の設定、表示、Archive クエリー、親への集約の材料集めを定義します。

## 概要

本プラグインは、公開日と更新日をコンテンツタイプごとに見える化します。更新日を出すか否かの判定、一覧のソートキー、子の `max(modified)` の集約は S2J Content Dates Service が行います。

スラッグは `s2j-content-dates` です。ライセンスは GPL-3.0-or-later です。テキストドメインは `s2j-content-dates` です。

kis-core は本機能の本体にしません。KIS のサイトは、本プラグインを有効化して使います。

## 位置付け

| 層 | 名称 | 役割 |
| --- | --- | --- |
| 計算 | [s2j-content-dates-service](https://github.com/stein2nd/s2j-content-dates-service) | 日付の正規化、表示可否、ソートキー、親への更新日集約。WordPress を知らない |
| 副作用 | **本プラグイン** | 設定、管理画面、ブロック、Archive の `WP_Query`、子投稿の収集 |

表示の日付と時刻は、WordPress「設定 > 一般」の日付形式と時刻形式だけを使います。独自の形式は持ちません。ライブラリは文字列を作りません。出力の直前に、この2つの設定で文字列にします。

公開日と更新日が同日でも、「更新日」は隠しません。差が小さい場合も隠しません。

## 目的

初版で、管理者が下記を終えることです。

* コンテンツタイプごとに、単体表示へ更新日を出すかを決める。
* Archive を持つタイプでは、更新日による絞り込みとソートを足すかを決める。
* Query Loop ブロックに、更新日ソートのコントロールを出すかを決める。サイトの初期値は出さない。すべてのタイプで出す指定と、タイプごとの除外ができる。
* 親タイプでは、子の更新日の最大を親の更新日にするかを決める。
* 単体テンプレートに、公開日と更新日のブロックを置く。

対象は、固定記事、投稿、登録済みの CPT です。メディアは行として表に出し、添付ファイルページの単体表示だけ初版に含めます。

## 非目標 (初版)

* 1件の投稿や固定記事だけを出したり隠したりする指定。表示はコンテンツタイプの on/off だけに従います。
* テーマの色、テンプレート階層、日付の見た目。
* メディアライブラリ一覧への更新日の列。日付の書き換えは [S2J Media Library Date Corrector](https://github.com/stein2nd/s2j-media-library-date-corrector) の仕事です。本プラグインは、保存済みの公開日と更新日を読むだけです。
* ピン留め一覧。非ピン集合の更新日ソートは、S2J Query Pinned 側が本プラグインの結果を受け取ります。本プラグインは Query Pinned を呼びません。
* サイトポリシーの「最終確認日」。方針を見直した日は S2J Site Policy Manager が持ち、投稿の更新日とは別です。
* `post_parent` 以外で結ばれた親子の集約。投稿メタや関連フィールドで結ぶ組は、子として集めません。

## サービスとの境界

入力は、タイムゾーン済みの公開日・更新日と、コンテンツタイプの設定レコードです。本プラグインが WordPress の値からそのレコードを作り、サービスの結果で出力とクエリーを決めます。

| 本プラグイン | サービス |
| --- | --- |
| `post_date` / `post_modified` を、サイトのタイムゾーン付きの値にして渡す | 比較できる値に正規化する |
| タイプ設定を渡す | 更新日を出すか否かを返す。1件ごとの指定は受けない |
| 子の日付レコードを集めて渡す | 子の `max(modified)` を親の更新日にする。適用するかは設定の on/off に従う |
| サービスのソートキーとフィルター値で `WP_Query` を組む | `WP_Query` は組まない |
| 「設定 > 一般」の形式で文字列にする | 表示文字列は作らない |

## 設定

画面は「設定 > コンテンツの日付」です。操作できるのは `manage_options` を持つユーザーです。

表は `WP_List_Table` 相当の標準テーブルです。1行が1コンテンツタイプです。見出しは「カスタム投稿タイプ」ではなく **コンテンツタイプ** です。

| 列 | 内容 |
| --- | --- |
| コンテンツタイプ | 表示名と登録名 |
| 単体で更新日を出す | on/off |
| Archive に更新日の絞り込みとソートを足す | on/off。`has_archive` がないタイプは、チェックを置かず無効 |
| 子の更新日を集約 | on/off と、子のコンテンツタイプを1つ |
| Query Loop に更新日ソートを出す | on/off。`page` と `attachment` も有効。Archive 列が無効でも置く |

表の上に、「Query Loop に更新日ソートを出す」を置きます。選択肢は「出さない」と「すべてのタイプで出す」です。保存先はオプション `s2j_content_dates_query_loop` で、値は `off` または `all` です。キーがない間は `off` です。

行に出すタイプは、`page`、`post`、`public` または `show_ui` の CPT、`attachment` です。`show_ui` だけで `public` ではない CPT も出します。管理画面に一覧があるタイプです。`revision`、`nav_menu_item`、`wp_template`、`wp_block` などの内部タイプは出しません。

`page` と `attachment` の Archive 列は無効です。固定記事に Archive はなく、メディアライブラリの一覧は初版で変えません。

保存先はオプション `s2j_content_dates_types` です。タイプ登録名をキーにし、値は下記の形です。

| タイプ登録名 | 値 |
| --- | --- |
| show_single | true / false / キーなし |
| show_archive | true / false / キーなし |
| aggregate | true / false / キーなし |
| child_type | 登録名、または空 |
| query_loop | true / false / キーなし |

新規タイプのデフォルトは off です。`query_loop` 以外は、キーがない (`null`) 間は off として読みます。タイプが登録された瞬間には書きません。利用者がその行のチェックを変えた場合だけ、on または off をそのタイプのキーに保存します。他のタイプの未保存は、そのまま残します。

`query_loop` は、サイトの値と合わせて読みます。サイトが `off` の場合、`true` のタイプにだけコントロールを出します。キーなしと `false` は出しません。サイトが `all` の場合、キーなしと `true` のタイプに出します。`false` のタイプには出しません。`all` の間に登録されたタイプは、除外するまで出ます。

登録が消えたタイプのキーは残します。同じ登録名が戻った場合、以前の on/off を読みます。

## 単体表示

ブロック名は `s2j/content-dates`、タイトルは「公開日と更新日」です。テーマの単体テンプレートに置きます。クエリーループの中では、その投稿を対象にします。

サービスの表示レコードが更新日を出すなら、公開日と更新日の両方を出します。出さないなら、公開日だけを出します。ラベルは「公開日」と「更新日」です。

同じ出力は、PHP から `s2j_content_dates_render( $post_id )` で呼べます。ハイブリッド期間の PHP テンプレート用です。ブロックもこの関数を使います。

## Archive

Archive 列が on のタイプだけ、メインクエリーが下記を受けます。デフォルトの並びは変えません。

* ソートは、クエリー変数 `orderby=modified` の場合、サービスのソートキーで更新日順にします。集約が on の親は、集約後の更新日をキーにします。
* 絞り込みは、クエリー変数 `modified_after` (サイトのタイムゾーンの日付) 以降の更新日に限る、という1つです。訪問者向けの絞り込みウィジェットは初版に置きません。

Archive 列が off のタイプでは、この2つのクエリー変数を無視します。

## Query Loop

Query Loop ブロックの並びの選択肢に「更新日」を足すかは、上のサイトの値と、そのブロックの対象タイプの `query_loop` で決めます。足す際、選ばれた並びは Archive と同じく、サービスのソートキーです。集約が on の親は、集約後の更新日をキーにします。ブロックを置いた際の初期の並びは変えません。

Archive 列が off のタイプでも、`query_loop` が出す側なら、このコントロールは出します。

## 親への集約

集約が on で、子タイプが1つ指定されている場合だけ行います。子は、その親投稿を `post_parent` に持つ、指定タイプの投稿です。KIS の `product` と `product_section` は、この指定の一例です。API の対象は、その組に限りません。投稿メタや関連フィールドで結ばれた組は、子として集めません。

子が0件の場合は、親自身の更新日のままです。子タイプの登録が消えている場合は、集約をせず、設定画面のその行に登録がない旨を出します。

## メディア

`attachment` の行は表にあります。デフォルトは off です。on の場合、添付ファイルページの単体表示が更新日を出します。メディアライブラリの列、フィルター、アップロード日とファイル更新の対応付けは初版で扱いません。

## 設計方針

[kis-wordpress エコシステム仕様](https://github.com/stein2nd/kis-wordpress/blob/main/docs_mod/specs.md) および [wp-plugin-spec](https://github.com/stein2nd/wp-plugin-spec) に従います。日付の判断はサービス側の純関数です。本プラグインはアダプタです。

設定画面、オプション、ブロック、`WP_Query`、子投稿の取得は本プラグインに置きます。

| 借用する原則 | 本プラグインでの意味 |
| --- | --- |
| 依存の向きは外 → 内 | 画面とクエリーがサービスを呼ぶ。サービスは WordPress を呼ばない |
| 内側はビジネスルール | 表示可否、ソートキー、集約はサービス |
| 外側は詳細 | オプション、ブロック、Archive |

Composer で `s2j/content-dates-service` を require します。参照は [S2J Slug Generater](https://github.com/stein2nd/s2j-slug-generater) が [`s2j/similarity-service`](https://packagist.org/packages/s2j/similarity-service) を Packagist の名前だけで require するのと同じです。`repositories` に `VCS` も `path` も書きません。

アンインストールではオプション `s2j_content_dates_types` と `s2j_content_dates_query_loop` を消します。投稿の日付は変えません。

## 関連リポジトリ

| 名称 | 種別 | 役割 |
| --- | --- | --- |
| **本プラグイン** | WP プラグイン | 設定、ブロック、Archive、子の収集 |
| [s2j-content-dates-service](https://github.com/stein2nd/s2j-content-dates-service) | Composer | 正規化、表示可否、ソートキー、集約 |
| [s2j-query-pinned-service](https://github.com/stein2nd/s2j-query-pinned-service) | Composer | ピン優先の組立。更新日は本プラグインの結果を受け取る側 |
| [s2j-media-library-date-corrector](https://github.com/stein2nd/s2j-media-library-date-corrector) | WP プラグイン | メディアの `post_date` をファイル配置の年月に合わせる。表示はしない |
| [kis-wordpress](https://github.com/stein2nd/kis-wordpress) | モノレポ | サイト専用プラグイン群。本機能は kis-core に抱え込まない |
| [kis2026_base](https://github.com/stein2nd/kis2026_base) | テーマ | 見た目。日付ブロックをテンプレートに置く |

## 実装順

1. 本ドラフトの合意。
2. 設定テーブルとオプション。未保存は off のまま、変えた行だけ保存する。
3. ブロックと `s2j_content_dates_render()`。
4. Archive の `orderby=modified` と `modified_after`。
5. Query Loop の更新日ソート。サイトの初期値は出さない。`all` と、タイプの `query_loop` で出し分ける。
6. `post_parent` による子の集約。
7. 添付ファイルページ。メディアライブラリの列は、その後に別ドラフトで扱う。

## 本ドラフトの提案

合意前の提案です。

* メニューは「設定 > コンテンツの日付」。表の見出しは「コンテンツタイプ」。
* 設定キーは `s2j_content_dates_types`。変えたタイプだけを書く。
* 単体の出力はブロック `s2j/content-dates`。更新日が off のタイプでは公開日だけを出す。
* Archive のデフォルトの並びは変えない。`orderby=modified` と `modified_after` を、on のタイプだけが受ける。
* Query Loop の更新日ソートは、初期値では出さない。`s2j_content_dates_query_loop` が `all` の場合は、`query_loop` が `false` のタイプを除いて出す。`off` の場合は、`query_loop` が `true` のタイプだけに出す。
* 子は `post_parent` で結ぶ。親の行で子タイプを1つ指定する。投稿メタや関連フィールドで結ぶ組は集約しない。
* メディアライブラリの一覧は初版で変えない。
* 表の行は、`page`、`post`、`public` または `show_ui` の CPT、`attachment` です。`show_ui` だけで `public` ではない CPT も出します。

## 改訂履歴

| 日付 | 内容 |
| --- | --- |
| 2026-10-04 | 初版ドラフト。コンテンツタイプ表、未保存は off、ブロック、Archive のクエリー変数、`post_parent` の集約、を記録 |
| 2026-10-05 | Composer の参照を、S2J Slug Generater と同じく Packagist のパッケージ名だけにする、と記録 |
| 2026-10-05 | Query Loop の更新日ソートは、初期値では出さない。すべてのタイプで出す指定と、タイプごとの除外を記録 |
| 2026-10-05 | 集約する子は `post_parent` を持つ投稿だけにする。投稿メタや関連フィールドで結ぶ組は集めない、と記録 |
| 2026-10-05 | `show_ui` だけで `public` ではない CPT も、設定の表に出す、と記録 |
