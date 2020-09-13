# Laravel Crawler

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nggiahao/crawler.svg?style=flat-square)](https://packagist.org/packages/nggiahao/crawler)
[![Total Downloads](https://img.shields.io/packagist/dt/nggiahao/crawler.svg?style=flat-square)](https://packagist.org/packages/nggiahao/crawler)

Package này có nhiêm vụ thu thập dữ liệu từ các website khác sử dụng Guzzle, Phantomjs hay Puppeteer.

Nó sử dụng Amphp để có thể chạy nhiều process 1 lúc.
## Installation

You can install the package via composer:

```bash
composer require nggiahao/crawler
```
```bash
php artisan vendor:publish --provider="Nggiahao\Crawler\CrawlerServiceProvider" --tag="config"
php artisan vendor:publish --provider="Nggiahao\Crawler\CrawlerServiceProvider" --tag="migrations"
php artisan migrate
```

Nếu bạn sử dụng Phantomjs hay Puppeteer thì hãy cài đặt chúng.

## Usage
### Step 1: Tạo Site
``` php
use Nggiahao\Crawler\SitesConfig\SiteAbstract;

class W123job extends SiteAbstract {

    public function rootUrl(): string
    {
        return 'https://123job.vn';
    }

    public function startUrls(): array {
        return [
            "https://123job.vn",
        ];
    }
    
    public function shouldCrawl( $url ) {
        return preg_match( "/^https:\/\/123job\.vn\/viec-lam\//", $url) || preg_match( "/^https:\/\/123job\.vn\/company\//", $url);
    }
    
    public function shouldGetData( $url ) {
        return preg_match( "/\/company\//", $url);
    }

    public function getInfoFromCrawler(Crawler $dom_crawler)
    {
        return parent::getInfoFromCrawler($dom_crawler);
    }
}
```
- `startUrls()` trả về mảng các url sẽ được sử dụng trong lần chạy đầu tiên 
- `shouldCrawl()` định nghĩa như nào là 1 url cần phi vào
- `shouldGetData()` định nghĩa như nào là 1 url cần lấy data
- `getInfoFromCrawler()` hàm này định nghĩa viêc lấy data như thế nào? (sử dụng [DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html))

### Step 2: Khai báo site
`config/crawler.php`
```
'site_config' => [
        W123job::class
    ]
```
### Step 3: Start
```php
    $sites = ['W123job'];
    $config = [
        'concurrency' => 10,
        'proxy'       => null,
        'browser'     => 'guzzle',
    ];
    $reset = false; //reset queue
    app(\Nggiahao\Crawler\Crawler::class)->run($sites, $config, $reset);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email giahao9899@gmail.com instead of using the issue tracker.

## Credits

- [Nguyen Gia Hao](https://github.com/nggiahao)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.