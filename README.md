# Laravel Devtoolbox

<div align="center">
  <img src="logo.png" alt="Laravel Devtoolbox" width="100">
  <p><strong>Swiss-army artisan CLI for Laravel — Scan, inspect, debug, and explore every aspect of your Laravel application from the command line.</strong></p>

  [![Latest Version](https://img.shields.io/packagist/v/grazulex/laravel-devtoolbox)](https://packagist.org/packages/grazulex/laravel-devtoolbox)
  [![Total Downloads](https://img.shields.io/packagist/dt/grazulex/laravel-devtoolbox)](https://packagist.org/packages/grazulex/laravel-devtoolbox)
  [![License](https://img.shields.io/github/license/grazulex/laravel-devtoolbox)](https://github.com/grazulex/laravel-devtoolbox/blob/main/LICENSE)
</div>

---

## ✨ Features

- 🔎 Deep Laravel project scanner
- 🧠 Route/controller/model introspection
- 🔍 Dead code & unused views detection
- 📦 Service container and provider analysis
- ⚙️ Config/env consistency and secrets audit
- 🔄 SQL, events, jobs, events, listeners trace
- 🧪 Test coverage, CI diffs, orphan routes/classes
- 📊 Export data to JSON, Markdown or Mermaid
- 🛠 DX utilities like lint combos & class dumps

## 📦 Installation

```bash
composer require --dev grazulex/laravel-devtoolbox
```

## 🧪 Example commands

```bash
php artisan dev:model:where-used App\Models\User
php artisan dev:routes:unused
php artisan dev:env:diff --against=.env.example
php artisan dev:model:graph --format=mermaid --out=models.mmd
php artisan dev:sql:trace --route=/orders
```

## 📁 Export formats supported

- ✅ Markdown
- ✅ JSON
- ✅ Mermaid (graphviz-style)
- ⏳ PDF (soon)

## 🚧 Roadmap

- [ ] Class interdependency graph
- [ ] Full CI test mapping
- [ ] Git hook integrations
- [ ] Live performance timeline

## 📝 License

Laravel Devtoolbox is open-sourced software licensed under the [MIT license](LICENSE).
