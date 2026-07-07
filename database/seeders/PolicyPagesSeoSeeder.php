<?php

namespace Database\Seeders;

use App\Models\Seo;
use Illuminate\Database\Seeder;

class PolicyPagesSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seos = [
            [
                'path' => '/about-us',
                'title' => '關於我們 - 威而鋼正品保證購買平台',
                'key_word' => '關於我們,威而鋼,原廠正品,隱密包裝,在線諮詢',
                'description' => '了解我們的使命與服務理念。本站提供美國輝瑞原廠正品威而鋼，全程隱密包裝發貨，安全支付，專業在線諮詢服務。',
            ],
            [
                'path' => '/privacy-policy',
                'title' => '隱私權政策 - 個人資料保護聲明',
                'key_word' => '隱私權政策,個人資料保護,Cookie,資料安全',
                'description' => '本站隱私權政策說明：我們如何收集、使用與保護您的個人資料，包括Cookie使用政策、資料安全措施及您的權利。',
            ],
            [
                'path' => '/terms-of-service',
                'title' => '服務條款 - 使用規範與交易须知',
                'key_word' => '服務條款,交易規範,購買條件,使用者權利',
                'description' => '本站服務條款詳述使用者資格、訂購交易規範、付款方式、退換貨條件等重要資訊，請於使用本站服務前仔細閱讀。',
            ],
            [
                'path' => '/return-policy',
                'title' => '退換貨與物流政策 - 配送與售後服務說明',
                'key_word' => '退換貨政策,物流配送,超商取貨,宅配,免運費',
                'description' => '了解本站的退換貨條件與流程、超商取貨及宅配配送方式、免運費條件，以及隱密包裝發貨承諾。',
            ],
            [
                'path' => '/medical-disclaimer',
                'title' => '醫療免責聲明 - 重要健康資訊告知',
                'key_word' => '醫療免責聲明,用藥安全,健康風險,藥品注意事項',
                'description' => '重要聲明：本站資訊僅供參考，不替代專業醫師建議。使用任何藥品前請先諮詢醫師，了解用法用量、禁忌症及可能的不良反應。',
            ],
        ];

        foreach ($seos as $seo) {
            Seo::updateOrCreate(
                ['path' => $seo['path']],
                $seo
            );
        }
    }
}
