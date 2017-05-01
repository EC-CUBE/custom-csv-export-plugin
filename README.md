# カスタムCSV出力プラグイン

## 概要

- 2系の、CSV出力項目設定＞高度な設定の機能です。
- SQLを作成し、CSVファイルをダウンロードすることができます。

## 機能一覧

### 管理

- 設定＞基本情報設定＞カスタムCSV出力メニューが追加されます。
- カスタムCSV出力メニューから、出力するためのSQLを登録することができます。
- 登録されたSQLを使用し、CSVファイルをエクスポートすることができます。

### その他

#### SQL作成時注意点
- DB名、カラム名は「`」で囲む。  
```
  例）`id` AS 'ID', `rank` AS 'ランク' FROM `mtb_sex`
```

#### 実行可能SQL
以下動作確認済。
- 副問合せ  
- カラム名変更可能
- 全カラム取得
- DISTINCTカラム
- Count
- Count + DISTINCT
- LIMIT AND OFFSET
- BETWEEN  

#### 実行不可SQL
- UPDATE文
- DELETE文

- DBMSの製品・バージョン違いによる記述形式の違いについて  
バージョンのDBMS（Postgre8.4等）によっては、
SQLの記述方式が異なる場合がございますので、ご注意下さい。

具体例）
Postgre8.4では日本語等を囲む際、
シングルクォートではなく、ダブルクォートを使用する必要がある。
```
NG）device_type_id AS '漢字セイ' FROM dtb_block
```
```
OK）device_type_id AS "漢字セイ" FROM dtb_block
```

