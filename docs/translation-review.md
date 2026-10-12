# Review Terjemahan Website (PRD-08 Fase 5)

Status semua terjemahan en / zh / ja / ko di bawah: **draf Claude — perlu dicek penutur asli sebelum go-live** (terutama zh/ja/ko, karena ini teks promosi).

- Sumber kebenaran: `lang/{id,en,zh,ja,ko}.json`. Key = teks Indonesia persis seperti di Blade. Ubah terjemahan langsung di file JSON bahasa terkait, **jangan ubah key**.
- Pesan validasi form kontak: `lang/{en,zh,ja,ko}/validation.php` (rule `required`, `string`, `email`, `max`) — juga draf.
- Konten dinamis (judul hero, paragraf Tentang Kami, produk, galeri) belum termasuk; diterjemahkan lewat admin di Fase 6.
- Beberapa teks Indonesia di situs aslinya memang berbahasa Inggris (mis. "Why Choose Us"); key-nya dibiarkan apa adanya supaya tampilan Indonesia tidak berubah.

Cara cek: buka `/en`, `/zh`, `/ja`, `/ko` beserta halaman lainnya (`/zh/produk`, dst.), bandingkan dengan tabel ini, tandai ✓ di kolom Cek.

## 1. Kalimat yang dipisah markup (baca berpasangan)

Kalimat ini dipotong oleh `<span>`/`<br>`/`<strong>` di desain dan tidak boleh diubah markup-nya. Pastikan gabungan potongan terbaca wajar, terutama urutan kata zh/ja/ko.

| Indonesia | en | zh | ja | ko | Cek |
|---|---|---|---|---|---|
| «Pilar Kualitas» + «PB. Ani Wijaya» | «Quality Pillars of» + «PB. Ani Wijaya» | «品质支柱» + «PB. Ani Wijaya» | «品質を支える柱» + «PB. Ani Wijaya» | «품질의 기둥» + «PB. Ani Wijaya» | |
| «Dari» + «Sawah Hingga Karung» | «From» + «Paddy Field to Sack» | «从» + «稻田到米袋» | «田んぼから» + «米袋まで» | «논에서» + «포대까지» | |
| «Warisan Indramayu,» + «Mutu Terjamin» | «The Legacy of Indramayu,» + «Guaranteed Quality» | «英德拉马尤的传承，» + «品质保证» | «インドラマユの伝統、» + «品質保証» | «인드라마유의 유산,» + «품질 보증» | |
| «Tujuan &» + «Arah Langkah» | «Purpose &» + «Our Direction» | «目标与» + «前进方向» | «目的と» + «進むべき道» | «목표와» + «나아갈 방향» | |
| «Nilai yang Kami» + «Pegang Teguh» | «The Values We» + «Hold Dear» | «我们» + «坚守的价值» | «私たちが» + «大切にしている価値» | «저희가» + «굳게 지키는 가치» | |
| «Siap menjadi bagian dari» + «cerita kami selanjutnya?» | «Ready to be part of» + «our next chapter?» | «准备好成为» + «我们下一个故事的一部分了吗？» | «私たちの» + «次の物語の一員になりませんか？» | «저희의» + «다음 이야기에 함께하시겠습니까?» | |
| «Beras Ketan Putih» + «Premium KEPALA» | «White Glutinous Rice» + «Premium Head Rice» | «白糯米» + «优质整粒米» | «白もち米» + «プレミアム整粒米» | «백찹쌀» + «프리미엄 완전립» | |
| «Standar Mutu yang» + «Tidak Kompromi» | «Quality Standards That» + «Never Compromise» | «品质标准» + «绝不妥协» | «妥協しない» + «品質基準» | «타협하지 않는» + «품질 기준» | |
| «Dari Sawah Menjadi» + «Produk Berkualitas» | «From Paddy Field to» + «Quality Products» | «从稻田到» + «优质产品» | «田んぼから» + «高品質な製品へ» | «논에서» + «고품질 제품까지» | |
| «Empat Pilar» + «Mutu Terjamin» | «Four Pillars of» + «Guaranteed Quality» | «四大支柱» + «品质保证» | «4つの柱» + «品質保証» | «네 가지 기둥» + «품질 보증» | |
| «Siap Skala» + «Bisnis Anda?» | «Ready to Scale» + «Your Business?» | «准备好扩大» + «您的业务吗？» | «ビジネス拡大の» + «準備はできていますか？» | «귀사의 비즈니스,» + «확장할 준비가 되셨나요?» | |
| «Mutu Terjamin,» + «Butir Demi Butir.» | «Guaranteed Quality,» + «Grain by Grain.» | «品质保证，» + «一粒一粒。» | «品質保証、» + «一粒一粒に。» | «품질 보증,» + «한 톨 한 톨까지.» | |
| «NIB Terdaftar &» + «Kualitas Tersertifikasi» | «Registered NIB &» + «Certified Quality» | «已注册 NIB 与» + «品质认证» | «NIB 登録済み・» + «認証された品質» | «NIB 등록 및» + «인증된 품질» | |
| «Hubungi Kami,» + «Kami Siap Melayani.» | «Contact Us,» + «We Are Ready to Serve.» | «联系我们，» + «我们竭诚为您服务。» | «お気軽にご連絡ください。» + «私たちがお応えします。» | «문의해 주세요,» + «언제든 도와드리겠습니다.» | |
| «Kirim Pesan ke» + «Tim Kami» | «Send a Message to» + «Our Team» | «发送消息给» + «我们的团队» | «メッセージを送る：» + «担当チーム» | «메시지 보내기:» + «저희 팀» | |
| «Isi formulir di bawah dan tim sales akan merespons dalam» + «1×24 jam kerja» | «Fill in the form below and our sales team will respond within» + «1×24 working hours» | «请填写下方表单，销售团队将在以下时间内回复：» + «1×24 个工作小时» | «下記フォームにご記入ください。営業チームより次の期間内にご返信します：» + «1営業日（24時間）以内» | «아래 양식을 작성해 주시면 영업팀이 다음 시간 내에 답변드립니다:» + «영업일 기준 24시간» | |
| «Untuk kunjungan pabrik, mohon konfirmasi via WhatsApp» + «minimal 1 hari sebelumnya» | «For factory visits, please confirm via WhatsApp» + «at least 1 day in advance» | «如需参观工厂，请通过 WhatsApp 预约确认：» + «至少提前 1 天» | «工場見学をご希望の場合は、WhatsApp でご連絡ください：» + «1日前までに» | «공장 방문은 WhatsApp으로 확인해 주세요:» + «최소 1일 전에» | |
| «Temukan Kami di» + «Indramayu» | «Find Us in» + «Indramayu» | «欢迎前往» + «英德拉马尤» | «アクセス：» + «インドラマユ» | «찾아오시는 길:» + «인드라마유» | |
| «Butuh Respons» + «Lebih Cepat?» | «Need a» + «Faster Response?» | «需要» + «更快的回复吗？» | «もっと» + «早い返信が必要ですか？» | «더 빠른» + «응답이 필요하신가요?» | |

## 2. Semua teks (252 key)

| # | Indonesia (key) | en | zh | ja | ko | Cek |
|---|---|---|---|---|---|---|
| 1 | "Setiap butir beras adalah janji kami untuk kualitas." | "Every grain of rice is our promise of quality." | "每一粒米都是我们对品质的承诺。" | 「一粒一粒のお米が、私たちの品質への約束です。」 | "한 톨 한 톨의 쌀이 품질에 대한 저희의 약속입니다." | |
| 2 | (opsional) | (optional) | （选填） | （任意） | (선택) | |
| 3 | 10 KG | 10 KG | 10 公斤 | 10 KG | 10 KG | |
| 4 | 100% Lokal Indramayu | 100% Local Indramayu | 100% 英德拉马尤本地 | インドラマユ産 100% | 100% 인드라마유 현지산 | |
| 5 | 100% Natural | 100% Natural | 100% 天然 | 100% 自然 | 100% 천연 | |
| 6 | 10KG | 10KG | 10公斤 | 10KG | 10KG | |
| 7 | 1×24 jam kerja | 1×24 working hours | 1×24 个工作小时 | 1営業日（24時間）以内 | 영업일 기준 24시간 | |
| 8 | 25 KG ★ | 25 KG ★ | 25 公斤 ★ | 25 KG ★ | 25 KG ★ | |
| 9 | 25KG | 25KG | 25公斤 | 25KG | 25KG | |
| 10 | 4 pilihan ukuran untuk berbagai kebutuhan: retail 5kg hingga grosir besar 50kg. | 4 size options for every need: from 5 kg retail to 50 kg bulk wholesale. | 4 种规格满足不同需求：从 5 公斤零售装到 50 公斤大宗批发装。 | 用途に合わせて4サイズ：小売用5kgから大口卸売用50kgまで。 | 다양한 용도에 맞춘 4가지 규격: 소매용 5kg부터 대량 도매용 50kg까지. | |
| 11 | 5 KG | 5 KG | 5 公斤 | 5 KG | 5 KG | |
| 12 | 50 KG | 50 KG | 50 公斤 | 50 KG | 50 KG | |
| 13 | 50KG | 50KG | 50公斤 | 50KG | 50KG | |
| 14 | 5KG | 5KG | 5公斤 | 5KG | 5KG | |
| 15 | 99,9% Murni | 99.9% Pure | 99.9% 纯净 | 純度 99.9% | 순도 99.9% | |
| 16 | 99.9% Clean | 99.9% Clean | 99.9% 洁净 | 99.9% クリーン | 99.9% 청결 | |
| 17 | :company di Indramayu, Jawa Barat. | :company in Indramayu, West Java, Indonesia. | 位于印度尼西亚西爪哇英德拉马尤的 :company。 | インドネシア西ジャワ州インドラマユの :company。 | 인도네시아 서자바 인드라마유의 :company. | |
| 18 | :tagline dari :address, melayani distribusi grosir dan retail dengan komitmen kualitas terjamin. | :tagline from :address, serving wholesale and retail distribution with a commitment to guaranteed quality. | 来自 :address 的 :tagline，以品质保证的承诺服务批发与零售分销。 | :address の :tagline。品質保証へのこだわりをもって卸売・小売流通に対応しています。 | :address의 :tagline. 품질 보증을 약속하며 도매 및 소매 유통을 지원합니다. | |
| 19 | AAA | AAA | AAA | AAA | AAA | |
| 20 | Adhesi Sempurna | Perfect Stickiness | 完美黏性 | 理想的な粘り | 완벽한 찰기 | |
| 21 | Alamat Pabrik | Factory Address | 工厂地址 | 工場所在地 | 공장 주소 | |
| 22 | All Rights Reserved. | All Rights Reserved. | 版权所有。 | All Rights Reserved. | All Rights Reserved. | |
| 23 | Apa yang ingin kami capai, dan bagaimana cara kami mencapainya. | What we aim to achieve, and how we get there. | 我们的目标，以及实现目标的方式。 | 私たちが目指すもの、そしてその実現方法。 | 저희가 이루고자 하는 것, 그리고 그 방법. | |
| 24 | Arah Langkah | Our Direction | 前进方向 | 進むべき道 | 나아갈 방향 | |
| 25 | Asal | Origin | 产地 | 産地 | 원산지 | |
| 26 | Asal Padi | Paddy Origin | 稻谷产地 | 稲の産地 | 벼 산지 | |
| 27 | Bagaimana Kami Bertindak | How We Act | 我们如何行动 | 私たちの取り組み | 저희의 실천 방식 | |
| 28 | Bebas Kotoran | Debris-Free | 无杂质 | 異物なし | 이물질 없음 | |
| 29 | Bebas Kotoran & Batu | Free of Debris & Stones | 无杂质、无石子 | 異物・石の混入なし | 이물질·돌 없음 | |
| 30 | Behind The Scenes | Behind The Scenes | 幕后 | 舞台裏 | 비하인드 스토리 | |
| 31 | Belum ada konten di kategori ini | No content in this category yet | 此类别暂无内容 | このカテゴリーにはまだコンテンツがありません | 이 카테고리에는 아직 콘텐츠가 없습니다 | |
| 32 | Beranda | Home | 首页 | ホーム | 홈 | |
| 33 | Beras Ketan Putih | White Glutinous Rice | 白糯米 | 白もち米 | 백찹쌀 | |
| 34 | Beras Ketan Putih PB. Ani Wijaya | PB. Ani Wijaya White Glutinous Rice | PB. Ani Wijaya 白糯米 | PB. Ani Wijaya の白もち米 | PB. Ani Wijaya 백찹쌀 | |
| 35 | Berhasil Terkirim! | Sent Successfully! | 发送成功！ | 送信が完了しました！ | 전송 완료! | |
| 36 | Berkontribusi pada ketahanan pangan nasional melalui supply chain yang konsisten. | Contributing to national food security through a consistent supply chain. | 通过稳定的供应链为国家粮食安全做出贡献。 | 安定したサプライチェーンを通じて国の食料安全保障に貢献します。 | 꾸준한 공급망으로 국가 식량 안보에 기여합니다. | |
| 37 | Bisnis Anda? | Your Business? | 您的业务吗？ | 準備はできていますか？ | 확장할 준비가 되셨나요? | |
| 38 | Buka di Google Maps | Open in Google Maps | 在 Google 地图中打开 | Google マップで開く | Google 지도에서 열기 | |
| 39 | Business Registration ID | Business Registration ID | 企业注册号 | 事業者登録番号 | 사업자 등록 번호 | |
| 40 | Butir Demi Butir. | Grain by Grain. | 一粒一粒。 | 一粒一粒に。 | 한 톨 한 톨까지. | |
| 41 | Butuh Respons | Need a | 需要 | もっと | 더 빠른 | |
| 42 | CERTIFIED | CERTIFIED | 已认证 | 認証済み | 인증됨 | |
| 43 | Cerita Kami | Our Story | 我们的故事 | 私たちの物語 | 저희 이야기 | |
| 44 | Ceritakan kebutuhan Anda — jumlah kebutuhan beras, lokasi pengiriman, jadwal, atau pertanyaan lainnya... | Tell us what you need — rice quantity, delivery location, schedule, or any other questions... | 请告诉我们您的需求——大米数量、送货地点、时间安排或其他问题…… | ご要望をお聞かせください（必要数量、配送先、スケジュール、その他のご質問など）… | 필요하신 내용을 알려 주세요 — 쌀 수량, 배송지, 일정 또는 기타 문의… | |
| 45 | Chat Sekarang | Chat Now | 立即咨询 | 今すぐチャット | 지금 채팅하기 | |
| 46 | Chat WhatsApp | Chat on WhatsApp | WhatsApp 咨询 | WhatsApp でチャット | WhatsApp 채팅 | |
| 47 | Chat WhatsApp Sales | Chat with Sales on WhatsApp | WhatsApp 联系销售 | WhatsApp で営業担当とチャット | WhatsApp으로 영업팀과 채팅 | |
| 48 | Chat WhatsApp Sekarang | Chat on WhatsApp Now | 立即通过 WhatsApp 咨询 | 今すぐ WhatsApp でチャット | 지금 WhatsApp으로 채팅 | |
| 49 | Dari | From | 从 | 田んぼから | 논에서 | |
| 50 | Dari Sawah Menjadi | From Paddy Field to | 从稻田到 | 田んぼから | 논에서 | |
| 51 | Dari kebutuhan retail hingga distribusi grosir massal, kapasitas pabrik kami memastikan supply chain selalu stabil tanpa kompromi. | From retail needs to large-scale wholesale distribution, our mill capacity keeps the supply chain stable without compromise. | 从零售需求到大规模批发分销，我们的工厂产能确保供应链始终稳定、毫不妥协。 | 小売から大規模な卸売流通まで、当工場の生産能力が妥協のない安定供給を支えます。 | 소매부터 대규모 도매 유통까지, 저희 공장의 생산 능력이 타협 없는 안정적인 공급망을 보장합니다. | |
| 52 | Data Anda aman dan hanya digunakan untuk merespons pertanyaan ini. | Your data is safe and only used to respond to this inquiry. | 您的信息将被安全保存，仅用于回复本次咨询。 | お客様の情報は安全に管理され、本お問い合わせへの回答にのみ使用されます。 | 고객님의 정보는 안전하게 보관되며 이 문의에 답변하는 데에만 사용됩니다. | |
| 53 | Distributor, retailer, atau UKM kuliner — kami siap menjadi partner supply beras berkualitas untuk usaha Anda. Hubungi kami untuk diskusi harga grosir dan kerjasama jangka panjang. | Distributors, retailers, or culinary SMEs — we are ready to be your quality rice supply partner. Contact us to discuss wholesale prices and long-term partnerships. | 无论是分销商、零售商还是餐饮中小企业——我们都乐意成为您优质大米的供应伙伴。欢迎联系我们洽谈批发价格与长期合作。 | 卸売業者、小売業者、飲食関連の中小企業の皆さま — 高品質なお米の供給パートナーとしてお手伝いします。卸売価格や長期的なお取引についてお気軽にご相談ください。 | 유통업체, 소매업체, 외식 중소기업 누구나 — 저희가 고품질 쌀 공급 파트너가 되어 드립니다. 도매 가격과 장기 협력에 대해 문의해 주세요. | |
| 54 | Distributor, retailer, atau pelaku UMKM kuliner — kami siap menjadi mitra pasokan beras ketan premium Anda. | Distributors, retailers, or culinary SMEs — we are ready to be your premium glutinous rice supply partner. | 无论是分销商、零售商还是餐饮中小企业——我们都乐意成为您优质糯米的供应伙伴。 | 卸売業者、小売業者、飲食関連の中小企業の皆さま — プレミアムもち米の供給パートナーとしてお手伝いします。 | 유통업체, 소매업체, 외식 중소기업 누구나 — 프리미엄 찹쌀 공급 파트너가 되어 드립니다. | |
| 55 | Distributor, retailer, atau pelaku UMKM kuliner — tim PB. Ani Wijaya siap merespons setiap pertanyaan kemitraan, harga grosir, atau kunjungan fasilitas. | Distributors, retailers, or culinary SMEs — the PB. Ani Wijaya team is ready to answer any questions about partnerships, wholesale prices, or facility visits. | 无论是分销商、零售商还是餐饮中小企业——PB. Ani Wijaya 团队随时解答有关合作、批发价格或参观工厂的任何问题。 | 卸売業者、小売業者、飲食関連の中小企業の皆さま — お取引、卸売価格、工場見学に関するご質問に PB. Ani Wijaya チームがお答えします。 | 유통업체, 소매업체, 외식 중소기업 누구나 — PB. Ani Wijaya 팀이 협력, 도매 가격, 시설 방문에 관한 모든 문의에 답변해 드립니다. | |
| 56 | Distributor, retailer, atau pemilik usaha kuliner — kami terbuka untuk diskusi kerjasama jangka panjang yang saling menguntungkan. | Distributors, retailers, or culinary business owners — we are open to discussing mutually beneficial long-term partnerships. | 无论是分销商、零售商还是餐饮业主——我们欢迎洽谈互利共赢的长期合作。 | 卸売業者、小売業者、飲食店オーナーの皆さま — 互いに利益のある長期的なお取引のご相談をお待ちしています。 | 유통업체, 소매업체, 외식업 대표님 누구나 — 상호 이익이 되는 장기 협력을 함께 논의하고 싶습니다. | |
| 57 | Dokumentasi Operasi | Operations Documentation | 运营记录 | 操業の記録 | 운영 기록 | |
| 58 | Email | Email | 电子邮箱 | メール | 이메일 | |
| 59 | Email Katalog | Email for Catalog | 邮件索取目录 | メールでカタログ請求 | 이메일로 카탈로그 요청 | |
| 60 | Empat Pilar | Four Pillars of | 四大支柱 | 4つの柱 | 네 가지 기둥 | |
| 61 | Established Legacy | Established Legacy | 传承基业 | 受け継がれる伝統 | 이어져 온 전통 | |
| 62 | Fasilitas | Facilities | 设施 | 設備 | 시설 | |
| 63 | Formulir Pertanyaan | Inquiry Form | 咨询表单 | お問い合わせフォーム | 문의 양식 | |
| 64 | Galeri | Gallery | 图库 | ギャラリー | 갤러리 | |
| 65 | Galeri - Dokumentasi Fasilitas & Produk | Gallery - Facility & Product Documentation | 图库 - 设施与产品记录 | ギャラリー - 設備・製品の記録 | 갤러리 - 시설 및 제품 기록 | |
| 66 | Galeri Lengkap | Full Gallery | 完整图库 | ギャラリー一覧 | 전체 갤러리 | |
| 67 | Galeri Sedang Dipersiapkan | Gallery Coming Soon | 图库筹备中 | ギャラリー準備中 | 갤러리 준비 중 | |
| 68 | Glutinous AAA | Glutinous AAA | 糯性 AAA | もち性 AAA | 찰기 AAA | |
| 69 | Grade | Grade | 等级 | グレード | 등급 | |
| 70 | Grade Tekstur | Texture Grade | 口感等级 | 食感グレード | 식감 등급 | |
| 71 | Halo PB. Ani Wijaya, saya ingin bertanya... | Hello PB. Ani Wijaya, I would like to ask... | 您好 PB. Ani Wijaya，我想咨询…… | PB. Ani Wijaya 様、お問い合わせです… | 안녕하세요 PB. Ani Wijaya, 문의드립니다... | |
| 72 | Halo PB. Ani Wijaya, saya ingin konsultasi harga grosir Beras Ketan Putih. | Hello PB. Ani Wijaya, I would like to discuss wholesale prices for White Glutinous Rice. | 您好 PB. Ani Wijaya，我想咨询白糯米的批发价格。 | PB. Ani Wijaya 様、白もち米の卸売価格について相談させてください。 | 안녕하세요 PB. Ani Wijaya, 백찹쌀 도매 가격을 상담하고 싶습니다. | |
| 73 | Halo PB. Ani Wijaya, saya ingin konsultasi mendesak. | Hello PB. Ani Wijaya, I have an urgent inquiry. | 您好 PB. Ani Wijaya，我有紧急事项需要咨询。 | PB. Ani Wijaya 様、至急ご相談したいことがあります。 | 안녕하세요 PB. Ani Wijaya, 급히 상담드리고 싶습니다. | |
| 74 | Halo PB. Ani Wijaya, saya tertarik dengan Beras Ketan Putih Premium. Mohon info harga grosir. | Hello PB. Ani Wijaya, I am interested in your Premium White Glutinous Rice. Please share your wholesale prices. | 您好 PB. Ani Wijaya，我对优质白糯米感兴趣，请告知批发价格。 | PB. Ani Wijaya 様、プレミアム白もち米に興味があります。卸売価格を教えてください。 | 안녕하세요 PB. Ani Wijaya, 프리미엄 백찹쌀에 관심이 있습니다. 도매 가격을 알려 주세요. | |
| 75 | Halo, saya tertarik dengan produk :company. Mohon info lebih lanjut. | Hello, I am interested in :company products. Please send me more information. | 您好，我对 :company 的产品感兴趣，请提供更多信息。 | こんにちは。:company の製品に興味があります。詳しい情報をお願いします。 | 안녕하세요, :company 제품에 관심이 있습니다. 자세한 정보를 부탁드립니다. | |
| 76 | Hanya padi ketan terbaik dari mitra petani Indramayu yang diterima. Kami memeriksa kadar air, ukuran butir, dan kebersihan secara manual sebelum diproses. | Only the finest glutinous paddy from our Indramayu partner farmers is accepted. We manually check moisture, grain size, and cleanliness before processing. | 我们只收购英德拉马尤合作农户最优质的糯稻。加工前会人工检查含水率、粒径和洁净度。 | インドラマユの契約農家から最上級のもち米の籾だけを受け入れています。加工前に水分量、粒の大きさ、清潔さを手作業で確認します。 | 인드라마유 협력 농가의 최상급 찰벼만 받습니다. 가공 전에 수분, 낟알 크기, 청결도를 직접 검사합니다. | |
| 77 | Heritage Quality | Heritage Quality | 传承品质 | 伝統の品質 | 전통의 품질 | |
| 78 | Hubungi Kami | Contact Us | 联系我们 | お問い合わせ | 문의하기 | |
| 79 | Hubungi Kami, | Contact Us, | 联系我们， | お気軽にご連絡ください。 | 문의해 주세요, | |
| 80 | Indramayu | Indramayu | 英德拉马尤 | インドラマユ | 인드라마유 | |
| 81 | Indramayu Barat | West Indramayu | 英德拉马尤西部 | 西インドラマユ | 서인드라마유 | |
| 82 | Inovasi Tradisional | Traditional Innovation | 传统中的创新 | 伝統と革新 | 전통 속 혁신 | |
| 83 | Inquiry | Inquiry | 询价 | お問い合わせ | 문의 | |
| 84 | Integritas | Integrity | 诚信 | 誠実さ | 정직 | |
| 85 | Isi formulir di bawah dan tim sales akan merespons dalam | Fill in the form below and our sales team will respond within | 请填写下方表单，销售团队将在以下时间内回复： | 下記フォームにご記入ください。営業チームより次の期間内にご返信します： | 아래 양식을 작성해 주시면 영업팀이 다음 시간 내에 답변드립니다: | |
| 86 | Jam Operasional | Opening Hours | 营业时间 | 営業時間 | 운영 시간 | |
| 87 | Jawa Barat | West Java | 西爪哇 | 西ジャワ | 서자바 | |
| 88 | Jelajahi Produk Kami | Explore Our Products | 浏览我们的产品 | 製品を見る | 제품 둘러보기 | |
| 89 | Jelajahi fasilitas penggilingan modern kami di Indramayu — dari sawah emas Jawa Barat hingga lini pengemasan presisi. Transparansi adalah pondasi PB. Ani Wijaya. | Explore our modern milling facility in Indramayu — from the golden rice fields of West Java to our precision packing line. Transparency is the foundation of PB. Ani Wijaya. | 走进我们位于英德拉马尤的现代化碾米厂——从西爪哇的金色稻田到精密包装线。透明是 PB. Ani Wijaya 的基石。 | インドラマユにある近代的な精米工場をご覧ください。西ジャワの黄金色の田んぼから精密な包装ラインまで。透明性こそ PB. Ani Wijaya の礎です。 | 인드라마유에 있는 현대식 도정 시설을 둘러보세요 — 서자바의 황금빛 논에서 정밀 포장 라인까지. 투명성은 PB. Ani Wijaya의 기반입니다. | |
| 90 | KEPALA | HEAD RICE | 整粒米 | 整粒（KEPALA） | 완전립(KEPALA) | |
| 91 | Kami Siap Melayani. | We Are Ready to Serve. | 我们竭诚为您服务。 | 私たちがお応えします。 | 언제든 도와드리겠습니다. | |
| 92 | Kantor & Produksi | Office & Production | 办公与生产 | 事務所・生産拠点 | 사무실 및 생산 | |
| 93 | Kemasan Fleksibel | Flexible Packaging | 灵活包装 | 選べるパッケージ | 유연한 포장 | |
| 94 | Kemitraan Jangka Panjang | Long-Term Partnership | 长期合作 | 長期的なパートナーシップ | 장기 파트너십 | |
| 95 | Kemurnian | Purity | 纯度 | 純度 | 순도 | |
| 96 | Kepala (Premium) | Head Rice (Premium) | 整粒米（优质） | 整粒米（プレミアム） | 완전립 (프리미엄) | |
| 97 | Kilau pearl-white | Pearl-white shine | 珍珠白光泽 | パールホワイトの輝き | 진주빛 광택 | |
| 98 | Kirim Inquiry | Send an Inquiry | 发送询价 | お問い合わせを送る | 문의 보내기 | |
| 99 | Kirim Pesan Sekarang | Send Message Now | 立即发送 | 今すぐ送信 | 지금 메시지 보내기 | |
| 100 | Kirim Pesan ke | Send a Message to | 发送消息给 | メッセージを送る： | 메시지 보내기: | |
| 101 | Komitmen kami pada kualitas terdokumentasi secara legal dan diaudit secara konsisten. | Our commitment to quality is legally documented and consistently audited. | 我们对品质的承诺有合法记录，并定期接受审核。 | 品質へのこだわりは法的に記録され、継続的に監査されています。 | 품질에 대한 저희의 약속은 법적으로 기록되어 있으며 꾸준히 감사를 받습니다. | |
| 102 | Kontak | Contact | 联系方式 | 連絡先 | 연락처 | |
| 103 | Kontak - Hubungi PB. Ani Wijaya | Contact - Get in Touch with PB. Ani Wijaya | 联系 - 联系 PB. Ani Wijaya | お問い合わせ - PB. Ani Wijaya | 연락처 - PB. Ani Wijaya 문의 | |
| 104 | Kontak Langsung | Direct Contact | 直接联系 | 直接のご連絡 | 직접 연락 | |
| 105 | Konten untuk kategori ini akan segera ditambahkan. | Content for this category will be added soon. | 此类别的内容即将上线。 | このカテゴリーのコンテンツは近日公開予定です。 | 이 카테고리의 콘텐츠가 곧 추가됩니다. | |
| 106 | Kualitas | Quality | 品质 | 品質 | 품질 | |
| 107 | Kualitas Tersertifikasi | Certified Quality | 品质认证 | 認証された品質 | 인증된 품질 | |
| 108 | Kualitas Utama | Quality First | 品质至上 | 品質第一 | 품질 최우선 | |
| 109 | Kualitas bukanlah tindakan, melainkan kebiasaan. Kami menerapkan uji laboratorium ketat untuk kelembaban, kemurnian, dan integritas butiran. | Quality is not an act, it is a habit. We apply strict laboratory testing for moisture, purity, and grain integrity. | 品质不是一时之举，而是一种习惯。我们对含水率、纯度和米粒完整度进行严格的实验室检测。 | 品質とは行動ではなく習慣です。水分量、純度、粒の状態について厳しいラボ検査を行っています。 | 품질은 한 번의 행동이 아니라 습관입니다. 수분, 순도, 낟알 상태에 대해 엄격한 실험실 검사를 실시합니다. | |
| 110 | Lahir dari tanah subur Indramayu, kami memadukan kearifan petani lokal dengan teknologi pengolahan modern untuk hasil terbaik. | Born from the fertile land of Indramayu, we combine the wisdom of local farmers with modern processing technology for the best results. | 我们源自英德拉马尤的沃土，将本地农户的智慧与现代加工技术相结合，追求最佳品质。 | インドラマユの肥沃な大地に生まれ、地元農家の知恵と最新の加工技術を組み合わせて最高の品質を追求しています。 | 인드라마유의 비옥한 땅에서 태어나, 현지 농부의 지혜와 현대 가공 기술을 결합해 최고의 결과를 만듭니다. | |
| 111 | Lebih Cepat? | Faster Response? | 更快的回复吗？ | 早い返信が必要ですか？ | 응답이 필요하신가요? | |
| 112 | Lihat Detail Produk | View Product Details | 查看产品详情 | 製品の詳細を見る | 제품 상세 보기 | |
| 113 | Lihat Produk | View Products | 查看产品 | 製品を見る | 제품 보기 | |
| 114 | Lihat Produk Dulu | See Products First | 先看看产品 | まず製品を見る | 제품 먼저 보기 | |
| 115 | Lihat Semua | View All | 查看全部 | すべて見る | 전체 보기 | |
| 116 | Lisensi resmi dari pemerintah Indonesia dengan nomor registrasi yang dapat diverifikasi publik. | Officially licensed by the Indonesian government with a publicly verifiable registration number. | 持有印度尼西亚政府正式许可，注册号可供公开查验。 | インドネシア政府の正式な許可を取得しており、登録番号は誰でも確認できます。 | 인도네시아 정부의 공식 허가를 받았으며 등록 번호는 누구나 확인할 수 있습니다. | |
| 117 | Local Sourced | Local Sourced | 本地采购 | 地元産 | 현지 조달 | |
| 118 | Logistik | Logistics | 物流 | 物流 | 물류 | |
| 119 | Lokasi Fasilitas | Facility Location | 设施位置 | 工場の所在地 | 시설 위치 | |
| 120 | Lokasi Utama | Main Location | 主要地点 | 本拠地 | 본사 위치 | |
| 121 | Lolos uji kualitas | Passed quality testing | 通过质量检测 | 品質検査合格 | 품질 검사 통과 | |
| 122 | Maksimal 5000 karakter | Maximum 5000 characters | 最多 5000 个字符 | 最大5000文字 | 최대 5000자 | |
| 123 | Mari Berkolaborasi | Let's Collaborate | 携手合作 | 一緒に取り組みましょう | 함께 협력해요 | |
| 124 | Memanfaatkan teknologi mutakhir untuk kemurnian dan hasil panen yang optimal. | Using cutting-edge technology for optimal purity and yield. | 运用先进技术，实现最佳纯度与出米率。 | 最先端の技術を活用し、最適な純度と歩留まりを実現します。 | 최신 기술을 활용해 최적의 순도와 수율을 달성합니다. | |
| 125 | Membangun kemitraan jangka panjang berdasarkan transparansi dan integritas. | Building long-term partnerships based on transparency and integrity. | 以透明和诚信为基础，建立长期合作关系。 | 透明性と誠実さに基づく長期的なパートナーシップを築きます。 | 투명성과 정직을 바탕으로 장기적인 파트너십을 구축합니다. | |
| 126 | Membangun kepercayaan dengan distributor dan retail partner melalui harga yang jujur, sertifikasi berat akurat (NIB), dan konsistensi yang tak tergoyahkan. | Building trust with distributors and retail partners through honest pricing, accurate weights (NIB certified), and unwavering consistency. | 通过诚实定价、准确称重（NIB 认证）和始终如一的稳定品质，赢得分销商与零售伙伴的信任。 | 誠実な価格、正確な計量（NIB 登録）、揺るぎない一貫性によって、卸売業者・小売パートナーとの信頼を築きます。 | 정직한 가격, 정확한 중량(NIB 인증), 흔들림 없는 일관성으로 유통·소매 파트너의 신뢰를 쌓습니다. | |
| 127 | Memberdayakan petani lokal dan memodernisasi rantai pasok beras tradisional — tanpa kehilangan jiwa budaya kami. | Empowering local farmers and modernising the traditional rice supply chain — without losing our cultural soul. | 赋能本地农户，推动传统大米供应链现代化——同时不失我们的文化根基。 | 地元農家を支え、伝統的な米のサプライチェーンを近代化します。私たちの文化の心を失うことなく。 | 현지 농부에게 힘을 실어 주고 전통 쌀 공급망을 현대화합니다 — 저희 문화의 정신을 잃지 않으면서. | |
| 128 | Menghormati kearifan pertanian tradisional sambil berinvestasi pada teknologi penggilingan modern untuk meminimalkan limbah dan memaksimalkan kualitas. | Honouring traditional farming wisdom while investing in modern milling technology to minimise waste and maximise quality. | 尊重传统农耕智慧，同时投资现代碾米技术，以减少浪费、提升品质。 | 伝統的な農業の知恵を大切にしながら、近代的な精米技術に投資し、無駄を減らして品質を高めています。 | 전통 농업의 지혜를 존중하면서 현대 도정 기술에 투자해 낭비는 줄이고 품질은 높입니다. | |
| 129 | Menjadi tolak ukur kualitas dan reliabilitas dalam industri penggilingan beras Indonesia. | To be the benchmark of quality and reliability in the Indonesian rice milling industry. | 成为印度尼西亚碾米行业品质与可靠性的标杆。 | インドネシアの精米業界における品質と信頼性の基準となること。 | 인도네시아 도정 산업에서 품질과 신뢰성의 기준이 되는 것. | |
| 130 | Mesin penggiling modern menghilangkan sekam dengan tetap menjaga integritas butir. Proses ini meminimalkan beras patah dan mempertahankan tekstur alami. | Modern milling machines remove the husk while keeping each grain intact. This minimises broken rice and preserves its natural texture. | 现代碾米机在去壳的同时保持米粒完整，减少碎米并保留天然口感。 | 最新の精米機が粒を傷つけずにもみ殻を取り除きます。砕米を最小限に抑え、本来の食感を保ちます。 | 현대식 도정기가 낟알을 온전히 지키면서 왕겨를 제거합니다. 싸라기를 최소화하고 자연스러운 식감을 유지합니다. | |
| 131 | Minggu | Sunday | 周日 | 日曜日 | 일요일 | |
| 132 | Minta Katalog | Request a Catalog | 索取产品目录 | カタログを請求 | 카탈로그 요청 | |
| 133 | Mis. Permintaan Harga Grosir / Kemitraan / Kunjungan Pabrik | E.g. Wholesale Price Request / Partnership / Factory Visit | 例如：批发报价 / 合作 / 参观工厂 | 例：卸売価格のお問い合わせ／お取引／工場見学 | 예: 도매 가격 문의 / 협력 / 공장 방문 | |
| 134 | Misi Strategis | Strategic Mission | 战略使命 | 戦略的ミッション | 전략적 사명 | |
| 135 | Mitra Distribusi | Distribution Partners | 分销伙伴 | 取引パートナー | 유통 파트너 | |
| 136 | Mohon periksa kembali: | Please check the following: | 请检查以下内容： | 以下をご確認ください： | 다음 내용을 확인해 주세요: | |
| 137 | Mulai Kerjasama dengan Kami | Start Partnering with Us | 开始与我们合作 | 私たちとお取引を始めましょう | 저희와 협력을 시작하세요 | |
| 138 | Mutu | Quality | 品质 | 品質 | 품질 | |
| 139 | Mutu Terjamin | Guaranteed Quality | 品质保证 | 品質保証 | 품질 보증 | |
| 140 | Mutu Terjamin, | Guaranteed Quality, | 品质保证， | 品質保証、 | 품질 보증, | |
| 141 | NIB | NIB | NIB（企业注册号） | NIB（事業者番号） | NIB (사업자 번호) | |
| 142 | NIB Terdaftar | Registered NIB | 已注册 NIB | NIB 登録済み | NIB 등록 | |
| 143 | NIB Terdaftar & | Registered NIB & | 已注册 NIB 与 | NIB 登録済み・ | NIB 등록 및 | |
| 144 | NIB: | NIB: | NIB： | NIB： | NIB: | |
| 145 | Nama Anda | Your name | 您的姓名 | お名前 | 성함 | |
| 146 | Nama Lengkap | Full Name | 姓名 | お名前 | 성명 | |
| 147 | Navigasi | Navigation | 导航 | メニュー | 메뉴 | |
| 148 | Nilai yang Kami | The Values We | 我们 | 私たちが | 저희가 | |
| 149 | Other | Other | 其他 | その他 | 기타 | |
| 150 | PB. Ani Wijaya | PB. Ani Wijaya | PB. Ani Wijaya | PB. Ani Wijaya | PB. Ani Wijaya | |
| 151 | PB. Ani Wijaya Logo | PB. Ani Wijaya Logo | PB. Ani Wijaya 标志 | PB. Ani Wijaya ロゴ | PB. Ani Wijaya 로고 | |
| 152 | PREMIUM GRADE | PREMIUM GRADE | 优质等级 | プレミアムグレード | 프리미엄 등급 | |
| 153 | Pabrik PB. Ani Wijaya | PB. Ani Wijaya Mill | PB. Ani Wijaya 工厂 | PB. Ani Wijaya の工場 | PB. Ani Wijaya 공장 | |
| 154 | Pasokan Grosir | Wholesale Supply | 批发供应 | 卸売供給 | 도매 공급 | |
| 155 | Pegang Teguh | Hold Dear | 坚守的价值 | 大切にしている価値 | 굳게 지키는 가치 | |
| 156 | Pemolesan Ganda | Double Polishing | 双重抛光 | 二度磨き | 이중 연마 | |
| 157 | Penggilingan Presisi | Precision Milling | 精密碾米 | 精密精米 | 정밀 도정 | |
| 158 | Pengiriman Nasional | Nationwide Delivery | 全国配送 | 全国配送 | 전국 배송 | |
| 159 | Per Karung | Per Sack | 每袋 | 1袋あたり | 포대당 | |
| 160 | Permintaan Katalog Harga Grosir | Wholesale Price Catalog Request | 索取批发价格目录 | 卸売価格カタログのご請求 | 도매 가격 카탈로그 요청 | |
| 161 | Pesan | Message | 留言 | メッセージ | 메시지 | |
| 162 | Pesan Anda berhasil dikirim! Tim kami akan menghubungi Anda segera. | Your message has been sent! Our team will contact you shortly. | 您的消息已发送！我们的团队会尽快与您联系。 | メッセージを送信しました。担当者より折り返しご連絡いたします。 | 메시지가 전송되었습니다! 곧 담당자가 연락드리겠습니다. | |
| 163 | Petunjuk Arah | Get Directions | 获取路线 | 経路を表示 | 길찾기 | |
| 164 | Pilar Kesuksesan | Pillars of Success | 成功支柱 | 成功の柱 | 성공의 기둥 | |
| 165 | Pilar Kualitas | Quality Pillars of | 品质支柱 | 品質を支える柱 | 품질의 기둥 | |
| 166 | Pilih bahasa | Choose language | 选择语言 | 言語を選択 | 언어 선택 | |
| 167 | Pilihan Kemasan Grosir | Wholesale Packaging Options | 批发包装规格 | 卸売用パッケージ | 도매 포장 옵션 | |
| 168 | Premium | Premium | 优质 | プレミアム | 프리미엄 | |
| 169 | Premium Grade | Premium Grade | 优质等级 | プレミアムグレード | 프리미엄 등급 | |
| 170 | Premium KEPALA | Premium Head Rice | 优质整粒米 | プレミアム整粒米 | 프리미엄 완전립 | |
| 171 | Produk | Products | 产品 | 製品 | 제품 | |
| 172 | Produk - Beras Ketan Putih Premium | Products - Premium White Glutinous Rice | 产品 - 优质白糯米 | 製品 - プレミアム白もち米 | 제품 - 프리미엄 백찹쌀 | |
| 173 | Produk Berkualitas | Quality Products | 优质产品 | 高品質な製品へ | 고품질 제품까지 | |
| 174 | Produk Unggulan | Featured Product | 明星产品 | 主力製品 | 대표 제품 | |
| 175 | Produksi | Processing | 生产 | 生産 | 생산 | |
| 176 | Proses | Process | 工艺 | 製法 | 공정 | |
| 177 | Proses Produksi | Production Process | 生产流程 | 生産工程 | 생산 공정 | |
| 178 | Proses pemolesan dua tahap memberikan kilau putih mutiara khas Beras Ketan KEPALA. Hasil akhir: butiran bersih, bebas kotoran, siap kemas. | A two-stage polishing process gives KEPALA glutinous rice its signature pearl-white shine. The result: clean, debris-free grains ready for packing. | 两道抛光工序赋予整粒糯米标志性的珍珠白光泽。成品米粒洁净、无杂质，可直接包装。 | 二段階の研磨工程により、整粒もち米ならではのパールホワイトの輝きが生まれます。仕上がりは異物のない清潔な粒で、そのまま包装できます。 | 2단계 연마 공정으로 완전립 찹쌀 특유의 진주빛 광택을 냅니다. 결과물: 이물질 없는 깨끗한 낟알, 바로 포장 가능. | |
| 179 | Puncak panen Indramayu yang diproses dengan presisi tinggi — menghasilkan butiran putih mutiara dengan kelengketan sempurna untuk kuliner premium Indonesia. | The peak of the Indramayu harvest, processed with high precision — producing pearl-white grains with perfect stickiness for premium Indonesian cuisine. | 英德拉马尤最佳收成，经高精度加工——米粒洁白如珍珠，黏性完美，适合高端印尼美食。 | インドラマユの収穫の最良の部分を高精度で加工。インドネシアの高級料理にぴったりの、パールホワイトで理想的な粘りの粒に仕上げています。 | 인드라마유 최상의 수확물을 정밀하게 가공해 — 프리미엄 인도네시아 요리에 어울리는 진주빛 낟알과 완벽한 찰기를 만듭니다. | |
| 180 | Purity | Purity | 纯度 | 純度 | 순도 | |
| 181 | Ready to Order? | Ready to Order? | 准备下单了吗？ | ご注文の準備はできましたか？ | 주문할 준비가 되셨나요? | |
| 182 | Region | Region | 产区 | 産地 | 산지 | |
| 183 | Resmi & Terpercaya | Official & Trusted | 正规可信 | 公認・信頼 | 공인 및 신뢰 | |
| 184 | Respons cepat melalui channel berikut | Quick response through these channels | 通过以下渠道快速回复 | 以下の窓口で迅速に対応します | 아래 채널로 빠르게 답변드립니다 | |
| 185 | Sabtu | Saturday | 周六 | 土曜日 | 토요일 | |
| 186 | Sawah Hingga Karung | Paddy Field to Sack | 稻田到米袋 | 米袋まで | 포대까지 | |
| 187 | Sejak Berdiri | Since Day One | 自创立以来 | 創業以来 | 창업 이래 | |
| 188 | Selama puluhan tahun, PB. Ani Wijaya menjadi pondasi produksi beras berkualitas — memadukan kearifan tradisional Indramayu dengan teknologi modern untuk menghidupi keluarga di seluruh Indonesia. | For decades, PB. Ani Wijaya has been a foundation of quality rice production — combining traditional Indramayu wisdom with modern technology to feed families across Indonesia. | 数十年来，PB. Ani Wijaya 一直是优质大米生产的基石——将英德拉马尤的传统智慧与现代技术相结合，为印度尼西亚各地的家庭提供食粮。 | 数十年にわたり、PB. Ani Wijaya は高品質な米づくりの礎となってきました。インドラマユの伝統の知恵と最新技術を組み合わせ、インドネシア全土の家庭の食卓を支えています。 | 수십 년 동안 PB. Ani Wijaya는 고품질 쌀 생산의 기반이 되어 왔습니다 — 인드라마유의 전통 지혜와 현대 기술을 결합해 인도네시아 전역의 가정을 먹여 살립니다. | |
| 189 | Seleksi Bahan Baku | Raw Material Selection | 原料精选 | 原料の選別 | 원료 선별 | |
| 190 | Semua | All | 全部 | すべて | 전체 | |
| 191 | Senin – Jumat | Monday – Friday | 周一至周五 | 月曜日～金曜日 | 월요일 – 금요일 | |
| 192 | Sertifikasi Resmi NIB | Official NIB Registration | NIB 正式认证 | NIB 公式登録 | NIB 공식 인증 | |
| 193 | Setiap butir beras ketan kami melewati seleksi ketat dari sawah hingga karung. Diproses dengan teknologi modern untuk menjaga kelengketan alami dan aroma khas Indramayu. | Every grain of our glutinous rice goes through strict selection from field to sack. Processed with modern technology to preserve its natural stickiness and the distinctive aroma of Indramayu. | 我们的每一粒糯米从稻田到米袋都经过严格筛选，并以现代技术加工，保留天然黏性与英德拉马尤特有的香气。 | 当社のもち米は田んぼから袋詰めまで厳しく選別されています。最新技術で加工し、自然な粘りとインドラマユならではの香りを守っています。 | 저희 찹쌀은 논에서 포대까지 엄격한 선별을 거칩니다. 현대 기술로 가공해 자연스러운 찰기와 인드라마유 특유의 향을 지킵니다. | |
| 194 | Setiap butir beras melalui pemeriksaan ketat di laboratorium internal. Hanya yang memenuhi standar "Kepala" yang masuk ke kemasan kami. | Every grain of rice goes through strict checks in our in-house laboratory. Only rice that meets the "Head Rice" standard goes into our packaging. | 每一粒米都经过内部实验室的严格检验。只有达到"整粒米"标准的大米才会被包装。 | すべての米粒を社内ラボで厳しく検査しています。「整粒米」の基準を満たしたものだけを袋詰めします。 | 모든 쌀알은 자체 실험실에서 엄격한 검사를 거칩니다. "완전립" 기준을 충족한 쌀만 포장합니다. | |
| 195 | Siap Skala | Ready to Scale | 准备好扩大 | ビジネス拡大の | 귀사의 비즈니스, | |
| 196 | Siap menjadi bagian dari | Ready to be part of | 准备好成为 | 私たちの | 저희의 | |
| 197 | Skala Reliable | Reliable Scale | 可靠产能 | 安定した供給力 | 믿을 수 있는 규모 | |
| 198 | Spesifikasi Produk | Product Specifications | 产品规格 | 製品仕様 | 제품 사양 | |
| 199 | Standar Kualitas | Quality Standards | 品质标准 | 品質基準 | 품질 기준 | |
| 200 | Standar Mutu yang | Quality Standards That | 品质标准 | 妥協しない | 타협하지 않는 | |
| 201 | Stock Tersedia | Stock Available | 库存充足 | 在庫あり | 재고 보유 | |
| 202 | Stok Terjamin | Guaranteed Stock | 库存保障 | 在庫を確保 | 재고 보장 | |
| 203 | Subjek | Subject | 主题 | 件名 | 제목 | |
| 204 | Tahun Pengalaman | Years of Experience | 年经验 | 年の実績 | 년의 경험 | |
| 205 | Tanya Harga Grosir | Ask for Wholesale Prices | 咨询批发价格 | 卸売価格を問い合わせる | 도매 가격 문의 | |
| 206 | Teknologi modern | Modern technology | 现代技术 | 最新技術 | 현대 기술 | |
| 207 | Teknologi optical sorting memastikan setiap karung bebas dari batu, kotoran, dan butir cacat. Konsisten setiap batch produksi. | Optical sorting technology ensures every sack is free of stones, debris, and defective grains — consistently, in every production batch. | 光学分选技术确保每袋大米不含石子、杂质和不良米粒，每批次品质如一。 | 光学選別技術により、すべての袋から石・異物・不良粒を取り除きます。どの生産ロットでも品質は一定です。 | 광학 선별 기술로 모든 포대에서 돌, 이물질, 불량 낟알을 제거합니다. 모든 생산 배치에서 일관된 품질. | |
| 208 | Tekstur | Texture | 口感 | 食感 | 식감 | |
| 209 | Tekstur ultra-lengket khas ketan KEPALA — ideal untuk lemper, wajik, dan kue tradisional premium. | The ultra-sticky texture of KEPALA glutinous rice — ideal for lemper, wajik, and premium traditional cakes. | 整粒糯米特有的超强黏性口感——非常适合制作 lemper、wajik 等高档传统糕点。 | 整粒もち米ならではの強い粘り — レンペル、ワジックなどの高級伝統菓子に最適です。 | 완전립 찹쌀 특유의 강한 찰기 — 렘퍼, 와직 등 프리미엄 전통 과자에 이상적입니다. | |
| 210 | Telepon | Phone | 电话 | 電話 | 전화 | |
| 211 | Temukan Kami di | Find Us in | 欢迎前往 | アクセス： | 찾아오시는 길: | |
| 212 | Tentang Kami | About Us | 关于我们 | 会社概要 | 회사 소개 | |
| 213 | Terdaftar dan diverifikasi oleh regulator Indonesia. Komitmen kualitas kami terdokumentasi secara legal. | Registered and verified by Indonesian regulators. Our commitment to quality is legally documented. | 已在印度尼西亚监管机构注册并通过核查。我们对品质的承诺有合法记录。 | インドネシアの規制当局に登録・確認済みです。品質へのこだわりは法的に記録されています。 | 인도네시아 규제 기관에 등록 및 검증되었습니다. 품질에 대한 약속은 법적으로 기록되어 있습니다. | |
| 214 | Terjamin | Guaranteed | 保证 | 保証 | 보증 | |
| 215 | Terverifikasi | Verified | 已核实 | 確認済み | 검증 완료 | |
| 216 | Terverifikasi NIB | NIB Verified | NIB 已核实 | NIB 確認済み | NIB 검증 완료 | |
| 217 | Texture | Texture | 口感 | 食感 | 식감 | |
| 218 | Tidak Kompromi | Never Compromise | 绝不妥协 | 品質基準 | 품질 기준 | |
| 219 | Tiga prinsip yang memandu setiap keputusan kami — dari pemilihan ladang hingga kemasan final. | Three principles that guide every decision we make — from choosing the fields to the final packaging. | 三项原则指引我们的每一个决定——从选田到最终包装。 | 田んぼ選びから最終的な包装まで、すべての判断を導く3つの原則。 | 논 선택부터 최종 포장까지, 모든 결정을 이끄는 세 가지 원칙. | |
| 220 | Tiga prinsip yang menjadikan kami pilihan utama distributor dan pelanggan setia di Jawa Barat dan sekitarnya. | Three principles that make us the first choice of distributors and loyal customers in West Java and beyond. | 三项原则，让我们成为西爪哇及周边地区分销商和忠实客户的首选。 | 西ジャワ内外の卸売業者や長年のお客様に選ばれ続ける3つの理由。 | 서자바와 그 너머의 유통업체와 단골 고객이 저희를 먼저 찾는 세 가지 이유. | |
| 221 | Tiga tahap presisi yang menjadikan setiap butir beras kami unggul di kelasnya. | Three precise stages that make every grain of our rice the best in its class. | 三道精密工序，让我们的每一粒米都成为同类中的佼佼者。 | 3つの精密な工程が、一粒一粒を最高品質に仕上げます。 | 세 단계의 정밀 공정으로 모든 낟알을 동급 최고로 만듭니다. | |
| 222 | Tim Kami | Our Team | 我们的团队 | 担当チーム | 저희 팀 | |
| 223 | Tim kami sedang mengkurasi foto dan video terbaik dari fasilitas produksi. Silakan kembali sebentar lagi! | Our team is curating the best photos and videos from our production facility. Please check back soon! | 我们的团队正在精选生产设施的优质照片和视频，请稍后再来！ | 生産設備の写真や動画を準備しています。もうしばらくお待ちください！ | 생산 시설의 사진과 영상을 준비하고 있습니다. 곧 다시 방문해 주세요! | |
| 224 | Toggle menu | Toggle menu | 切换菜单 | メニューを開閉 | 메뉴 열기/닫기 | |
| 225 | Tradisi Indramayu | Indramayu Tradition | 英德拉马尤传统 | インドラマユの伝統 | 인드라마유의 전통 | |
| 226 | Transparansi adalah pondasi kepercayaan kami. Lihat proses operasi kami secara langsung. | Transparency is the foundation of our trust. See our operations first-hand. | 透明是我们赢得信任的基础。亲眼看看我们的运营流程。 | 透明性は信頼の土台です。私たちの操業の様子を直接ご覧ください。 | 투명성은 신뢰의 기반입니다. 저희의 운영 과정을 직접 확인하세요. | |
| 227 | Tujuan & | Purpose & | 目标与 | 目的と | 목표와 | |
| 228 | Tutup | Closed | 休息 | 休業 | 휴무 | |
| 229 | Ultra-Glutinous | Ultra-Glutinous | 超强糯性 | 強いもち性 | 강한 찰기 | |
| 230 | Ultra-Lengket | Ultra-Sticky | 超强黏性 | 強い粘り | 강한 찰기 | |
| 231 | Untuk konsultasi mendesak atau permintaan harga grosir hari ini, langsung hubungi tim sales kami via WhatsApp. | For urgent consultations or same-day wholesale price requests, contact our sales team directly on WhatsApp. | 如需紧急咨询或当天批发报价，请直接通过 WhatsApp 联系我们的销售团队。 | お急ぎのご相談や当日の卸売価格のお問い合わせは、WhatsApp で営業チームに直接ご連絡ください。 | 긴급 상담이나 당일 도매 가격 문의는 WhatsApp으로 영업팀에 바로 연락해 주세요. | |
| 232 | Untuk kunjungan pabrik, mohon konfirmasi via WhatsApp | For factory visits, please confirm via WhatsApp | 如需参观工厂，请通过 WhatsApp 预约确认： | 工場見学をご希望の場合は、WhatsApp でご連絡ください： | 공장 방문은 WhatsApp으로 확인해 주세요: | |
| 233 | Video | Video | 视频 | 動画 | 영상 | |
| 234 | Visi & Misi | Vision & Mission | 愿景与使命 | ビジョンとミッション | 비전과 미션 | |
| 235 | Visi Kami | Our Vision | 我们的愿景 | 私たちのビジョン | 저희의 비전 | |
| 236 | Waktu Pelayanan | Service Hours | 服务时间 | 対応時間 | 서비스 시간 | |
| 237 | Warisan Indramayu, | The Legacy of Indramayu, | 英德拉马尤的传承， | インドラマユの伝統、 | 인드라마유의 유산, | |
| 238 | WhatsApp | WhatsApp | WhatsApp | WhatsApp | WhatsApp | |
| 239 | Why Choose Us | Why Choose Us | 为何选择我们 | 選ばれる理由 | 저희를 선택하는 이유 | |
| 240 | cerita kami selanjutnya? | our next chapter? | 我们下一个故事的一部分了吗？ | 次の物語の一員になりませんか？ | 다음 이야기에 함께하시겠습니까? | |
| 241 | email@perusahaan.com | email@company.com | email@company.com | email@company.com | email@company.com | |
| 242 | minimal 1 hari sebelumnya | at least 1 day in advance | 至少提前 1 天 | 1日前までに | 최소 1일 전에 | |
| 243 | — The Master's Choice | — The Master's Choice | — 大师之选 | — 匠の選択 | — 장인의 선택 | |
| 244 | — Tim PB. Ani Wijaya | — The PB. Ani Wijaya Team | — PB. Ani Wijaya 团队 | — PB. Ani Wijaya チーム | — PB. Ani Wijaya 팀 | |
| 245 | ★ Kemasan unggulan untuk distributor | ★ Preferred packaging for distributors | ★ 分销商首选包装 | ★ 卸売業者に人気のサイズ | ★ 유통업체 추천 포장 | |
| 246 | ⚙️ Processing | ⚙️ Processing | ⚙️ 加工 | ⚙️ 加工 | ⚙️ 가공 | |
| 247 | ✓ Quality Control | ✓ Quality Control | ✓ 质量控制 | ✓ 品質管理 | ✓ 품질 관리 | |
| 248 | 🌾 Origin | 🌾 Origin | 🌾 产地 | 🌾 産地 | 🌾 산지 | |
| 249 | 🍚 Final Product | 🍚 Final Product | 🍚 成品 | 🍚 製品 | 🍚 완제품 | |
| 250 | 🏭 Facility | 🏭 Facility | 🏭 设施 | 🏭 設備 | 🏭 시설 | |
| 251 | 📦 Logistics | 📦 Logistics | 📦 物流 | 📦 物流 | 📦 물류 | |
| 252 | 📷 Other | 📷 Other | 📷 其他 | 📷 その他 | 📷 기타 | |
