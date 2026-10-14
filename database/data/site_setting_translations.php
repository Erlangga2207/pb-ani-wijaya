<?php

// Isi awal pengaturan website yang bisa diterjemahkan (PRD-08 Fase 6, keputusan Fase 0 no. 1, 3, 4).
// Dipakai migration (data lama) dan SiteSettingSeeder (database baru).
// 'old' = nilai default seeder lama. Semua teks di sini DRAF → docs/translation-review.md (perlu dicek penutur asli).

return [
    'hero_badge' => [
        'old' => "INDRAMAYU'S FINEST",
        'id' => "INDRAMAYU'S FINEST", // tagline brand, sengaja tidak diterjemahkan (keputusan Erlan 2026-10-13)
        'en' => "INDRAMAYU'S FINEST",
        'zh' => '英德拉马尤精选',
        'ja' => 'インドラマユ最高品質',
        'ko' => '인드라마유 최고 품질',
    ],
    'hero_title' => [
        'old' => 'Heritage of Excellence',
        'id' => 'Heritage of Excellence', // tagline brand, sengaja tidak diterjemahkan
        'en' => 'Heritage of Excellence',
        'zh' => '卓越传承',
        'ja' => '受け継がれる卓越',
        'ko' => '탁월함의 유산',
    ],
    'hero_subtitle' => [
        'old' => 'Mutu Terjamin',
        'id' => 'Mutu Terjamin',
        'en' => 'Guaranteed Quality',
        'zh' => '品质保证',
        'ja' => '品質保証',
        'ko' => '품질 보증',
    ],
    'hero_description' => [
        'old' => 'Elevating the standard of Indonesian staple grain through meticulous cultivation and modern processing techniques in the heart of Indramayu.',
        'id' => 'Meningkatkan standar bahan pangan pokok Indonesia melalui budidaya yang teliti dan teknik pengolahan modern di jantung Indramayu.',
        'en' => 'Elevating the standard of Indonesian staple grain through meticulous cultivation and modern processing techniques in the heart of Indramayu.',
        'zh' => '以精心的种植和现代化加工技术，在英德拉马尤腹地提升印度尼西亚主粮的品质标准。',
        'ja' => 'インドラマユの中心で、丁寧な栽培と最新の加工技術により、インドネシアの主食の水準を高めています。',
        'ko' => '인드라마유의 중심에서 정성스러운 재배와 현대적인 가공 기술로 인도네시아 주식의 기준을 높입니다.',
    ],
    'company_tagline' => [
        'old' => 'Premium Rice Producer',
        'id' => 'Premium Rice Producer', // teks brand, sengaja tidak diterjemahkan (keputusan Erlan 2026-10-13)
        'en' => 'Premium Rice Producer',
        'zh' => '优质大米生产商',
        'ja' => 'プレミアム米メーカー',
        'ko' => '프리미엄 쌀 생산업체',
    ],
    'about_title' => [
        'old' => 'A Journey of Resilience and Excellence',
        'id' => 'Perjalanan Ketekunan & Keunggulan',
        'en' => 'A Journey of Resilience and Excellence',
        'zh' => '坚韧与卓越之路',
        'ja' => '粘り強さと卓越の歩み',
        'ko' => '끈기와 탁월함의 여정',
    ],
    'about_paragraph_1' => [
        'old' => 'Founded in the heart of Indramayu, West Java, PB. Ani Wijaya began as a humble family dream to provide consistent, high-quality rice to local communities. What started as a single mill has evolved into a premier industrial rice producer, serving wholesale distributors across Indonesia.',
        'id' => 'Berdiri di jantung Indramayu, Jawa Barat, PB. Ani Wijaya bermula dari mimpi sederhana sebuah keluarga untuk menyediakan beras berkualitas tinggi yang konsisten bagi masyarakat sekitar. Berawal dari satu penggilingan, kini kami berkembang menjadi produsen beras terkemuka yang melayani distributor grosir di seluruh Indonesia.',
        'en' => 'Founded in the heart of Indramayu, West Java, PB. Ani Wijaya began as a humble family dream to provide consistent, high-quality rice to local communities. What started as a single mill has evolved into a premier industrial rice producer, serving wholesale distributors across Indonesia.',
        'zh' => 'PB. Ani Wijaya 创立于西爪哇英德拉马尤腹地，起初只是一个家庭的朴素梦想——为当地社区稳定供应优质大米。从一家碾米厂起步，如今已发展成为领先的大米生产商，服务印度尼西亚各地的批发分销商。',
        'ja' => '西ジャワ州インドラマユの中心で創業した PB. Ani Wijaya は、地域の人々に高品質なお米を安定して届けたいという家族のささやかな夢から始まりました。一軒の精米所から出発し、今ではインドネシア全土の卸売業者に供給する有数の米メーカーへと成長しました。',
        'ko' => '서자바 인드라마유의 중심에서 설립된 PB. Ani Wijaya는 지역 주민에게 품질 좋은 쌀을 꾸준히 공급하고자 한 한 가족의 소박한 꿈에서 시작되었습니다. 도정소 하나로 출발해 이제는 인도네시아 전역의 도매 유통업체에 공급하는 대표적인 쌀 생산업체로 성장했습니다.',
    ],
    'about_paragraph_2' => [
        'old' => 'The name "Ani Wijaya" is synonymous with trust. We understand that rice is more than just a staple; it is the center of the Indonesian dining table. This profound responsibility drives our "Mutu Terjamin" (Guaranteed Quality) philosophy, ensuring that every bag leaving our warehouse meets the highest standards of purity, texture, and taste.',
        'id' => 'Nama "Ani Wijaya" identik dengan kepercayaan. Kami memahami bahwa beras lebih dari sekadar bahan pokok; beras adalah pusat meja makan keluarga Indonesia. Tanggung jawab besar ini mendorong filosofi "Mutu Terjamin" kami, memastikan setiap karung yang keluar dari gudang memenuhi standar tertinggi dalam kemurnian, tekstur, dan rasa.',
        'en' => 'The name "Ani Wijaya" is synonymous with trust. We understand that rice is more than just a staple; it is the center of the Indonesian dining table. This profound responsibility drives our "Mutu Terjamin" (Guaranteed Quality) philosophy, ensuring that every bag leaving our warehouse meets the highest standards of purity, texture, and taste.',
        'zh' => '"Ani Wijaya" 这个名字代表着信赖。我们深知，大米不只是主食，更是印度尼西亚家庭餐桌的中心。这份重大的责任推动着我们"品质保证"（Mutu Terjamin）的理念，确保每一袋出库的大米在纯度、口感和味道上都达到最高标准。',
        'ja' => '「Ani Wijaya」という名前は信頼の証です。お米は単なる主食ではなく、インドネシアの食卓の中心です。この大きな責任が「品質保証（Mutu Terjamin）」という私たちの理念を支え、倉庫から出荷されるすべての袋が純度・食感・味のすべてで最高水準を満たすようにしています。',
        'ko' => '"Ani Wijaya"라는 이름은 곧 신뢰입니다. 쌀은 단순한 주식이 아니라 인도네시아 가정 식탁의 중심입니다. 이 막중한 책임감이 "품질 보증(Mutu Terjamin)" 철학을 이끌며, 창고를 떠나는 모든 포대가 순도, 식감, 맛에서 최고 기준을 충족하도록 합니다.',
    ],
    // Key baru (keputusan Fase 0 no. 3)
    'seo_meta_title' => [
        'old' => null,
        'id' => 'Pabrik & Supplier Beras Ketan Premium di Indramayu',
        'en' => 'Premium Glutinous Rice Mill & Supplier in West Java',
        'zh' => '西爪哇优质糯米加工厂与供应商',
        'ja' => '西ジャワのプレミアムもち米精米所・サプライヤー',
        'ko' => '서자바 프리미엄 찹쌀 정미소 및 공급업체',
    ],
    'seo_meta_description' => [
        'old' => null,
        'id' => 'PB. Ani Wijaya, pabrik beras ketan di perbatasan Indramayu–Subang, Jawa Barat. Supplier beras ketan putih premium untuk distributor, restoran, dan industri makanan di Indonesia dan mancanegara.',
        'en' => 'PB. Ani Wijaya is an Indonesian glutinous rice supplier and rice mill in West Java, on the Indramayu–Subang border. Premium white glutinous rice for distributors, restaurants, and food manufacturers in Indonesia and abroad.',
        'zh' => 'PB. Ani Wijaya 是位于西爪哇英德拉马尤与苏邦交界处的印度尼西亚糯米加工厂和供应商，为国内外分销商、餐厅及食品企业供应优质白糯米。',
        'ja' => 'PB. Ani Wijaya は西ジャワ州インドラマユとスバンの境にある、インドネシアのもち米精米所・サプライヤーです。国内外の卸売業者、レストラン、食品メーカーへ高品質な白もち米を供給しています。',
        'ko' => 'PB. Ani Wijaya는 서자바 인드라마유와 수방 경계에 위치한 인도네시아 찹쌀 정미소이자 공급업체로, 국내외 유통업체, 레스토랑, 식품 제조업체에 프리미엄 백찹쌀을 공급합니다.',
    ],
];
