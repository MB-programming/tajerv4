<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$lang      = lang();
$dir       = isRtl() ? 'rtl' : 'ltr';
$s         = setting();
$products  = db()->query("SELECT * FROM products WHERE status='active' ORDER BY sort_order")->fetchAll();
$numbers   = db()->query("SELECT * FROM numbers WHERE status='active' ORDER BY sort_order")->fetchAll();
$teams     = db()->query("SELECT * FROM teams WHERE status='active' ORDER BY sort_order")->fetchAll();
$partners  = db()->query("SELECT * FROM partners WHERE status='active' ORDER BY sort_order")->fetchAll();
$branches  = db()->query("SELECT * FROM branches WHERE status='active' ORDER BY sort_order")->fetchAll();
$flash     = getFlash();
$storeUrl  = 'https://tajagri.sa/';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>شركة تاج للحلول الزراعية</title>
    <link rel="icon" href="assets/images/favicon.svg">
    <link href="assets/css/style.css" rel="stylesheet">
    <?php if ($lang === 'en'): ?>
    <link href="assets/css/ltr-style.css" rel="stylesheet">
    <?php endif; ?>
    <link href="assets/css/responsive.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <?php if (!empty($s['head_code'])) echo $s['head_code']; ?>
    <style>
        .branch-item { cursor: pointer; transition: color .2s; }
        .branch-item:hover { color: #3AC0CA; }
        .branch-modal-content { border-radius: 12px; border: none; overflow: hidden; }
        .branch-modal-header { background: linear-gradient(135deg, #35285C, #4a3a7d); color: #fff; padding: 18px 24px; }
        .branch-modal-title { font-weight: 700; font-size: 18px; color: #fff; }
        .branch-modal-close { color: #fff; opacity: 1; font-size: 22px; }
        .branch-detail-row { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; font-size: 15px; color: #444; }
        .branch-detail-icon { min-width: 24px; text-align: center; font-size: 18px; color: #35285C; margin-top: 1px; }
        .branch-modal-footer { background: #f8f9fa; border-top: 1px solid #eee; padding: 16px 24px; justify-content: center; }
        .branch-map-btn { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #35285C, #4a3a7d); color: #fff !important; padding: 10px 28px; border-radius: 25px; font-weight: 600; text-decoration: none !important; transition: opacity .2s; }
        .branch-map-btn:hover { opacity: .85; }
        .about-cta-btn { display: inline-block; background: var(--main-color); color: #fff !important; padding: 12px 32px; border-radius: 25px; font-weight: 600; text-decoration: none !important; transition: opacity .2s; font-size: 15px; margin-top: 20px; }
        .about-cta-btn:hover { opacity: .85; }
        .alert-success-custom { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 15px; }
        .alert-error-custom { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 15px; }
        /* Partners slider */
        .pslider-wrap { position: relative; padding: 0 54px; }
        .pslider-vp { overflow: hidden; }
        .pslider-track { display: flex; align-items: center; transition: transform .5s cubic-bezier(.4,0,.2,1); }
        .pslider-item { flex: 0 0 calc(100% / 6); min-width: 0; display: flex; align-items: center; justify-content: center; padding: 12px 16px; }
        .pslider-item img { max-width: 130px; max-height: 80px; width: auto; height: auto; object-fit: contain; filter: grayscale(30%); transition: filter .2s; }
        .pslider-item img:hover { filter: grayscale(0%); }
        .pslider-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 50%; border: 2px solid #dde1ec; background: #fff; color: #35285C; font-size: 14px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: .2s; box-shadow: 0 2px 10px rgba(0,0,0,.1); z-index: 5; }
        .pslider-btn:hover { background: #35285C; color: #fff; border-color: #35285C; }
        .pslider-btn-right { right: 0; }
        .pslider-btn-left  { left: 0; }
        @media(max-width: 991px) { .pslider-item { flex: 0 0 25%; } .pslider-wrap { padding: 0 44px; } }
        @media(max-width: 575px) { .pslider-item { flex: 0 0 50%; } }
        .pslider-item.team-item { flex: 0 0 25%; padding: 8px; }
        @media(max-width: 991px) { .pslider-item.team-item { flex: 0 0 50%; } }
        @media(max-width: 575px) { .pslider-item.team-item { flex: 0 0 100%; } }
        .pslider-item.prod-item { flex: 0 0 20%; padding: 8px; }
        @media(max-width: 991px) { .pslider-item.prod-item { flex: 0 0 33.333%; } }
        @media(max-width: 575px) { .pslider-item.prod-item { flex: 0 0 50%; } }
        /* Modal close button */
        .branch-modal-close { background: rgba(255,255,255,.15); border: 2px solid rgba(255,255,255,.4); border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 15px; cursor: pointer; transition: .2s; padding: 0; flex-shrink: 0; }
        .branch-modal-close:hover { background: rgba(255,255,255,.3); border-color: rgba(255,255,255,.75); }
    </style>
</head>
<body>

<div class="main-wrapper">

    <!-- Header -->
    <header id="header">
        <div class="container">
            <div class="logo-site">
                <a href="index.php">
                    <?php if ($s['logo']): ?>
                        <img src="<?= e(imgUrl($s['logo'])) ?>" alt="Logo">
                    <?php else: ?>
                        <img src="assets/images/logo.svg" alt="">
                    <?php endif; ?>
                </a>
            </div>
            <?php
            $navItems = [];
            if (!empty($s['nav_json'])) {
                $decoded = json_decode($s['nav_json'], true);
                if (is_array($decoded)) $navItems = $decoded;
            }
            if (empty($navItems)) {
                $navItems = [
                    ['label_ar' => 'منتجاتنا',         'label_en' => 'Our Products',  'href' => '#products',      'enabled' => true, 'is_button' => false],
                    ['label_ar' => 'شركاؤنا',           'label_en' => 'Our Partners',  'href' => '#partners',      'enabled' => true, 'is_button' => false],
                    ['label_ar' => 'فروعنا',            'label_en' => 'Our Branches',  'href' => '#branches',      'enabled' => true, 'is_button' => false],
                    ['label_ar' => 'عن تاج',            'label_en' => 'About Taj Agri','href' => '#about-section', 'enabled' => true, 'is_button' => false],
                    ['label_ar' => 'تواصل معنا',        'label_en' => 'Contact Us',    'href' => '#footer',        'enabled' => true, 'is_button' => false],
                    ['label_ar' => 'المتجر الإلكتروني','label_en' => 'Online Store',  'href' => $storeUrl,        'enabled' => true, 'is_button' => true],
                ];
            }
            ?>
            <ul class="main_menu clearfix">
                <?php foreach ($navItems as $ni):
                    if (empty($ni['enabled'])) continue;
                    $nlabel  = $lang === 'ar' ? ($ni['label_ar'] ?? '') : ($ni['label_en'] ?? '');
                    $nhref   = htmlspecialchars($ni['href'] ?? '#', ENT_QUOTES, 'UTF-8');
                    $nlabelE = htmlspecialchars($nlabel, ENT_QUOTES, 'UTF-8');
                ?>
                <?php if (!empty($ni['is_button'])): ?>
                <li><a class="page-scroll btn-site" href="<?= $nhref ?>"><span><?= $nlabelE ?></span></a></li>
                <?php else: ?>
                <li><a class="page-scroll" href="<?= $nhref ?>"><?= $nlabelE ?></a></li>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php if ($lang === 'ar'): ?>
                <li class="lang-site"><a href="lang.php?l=en" class="page-scroll"><span>EN</span></a></li>
                <?php else: ?>
                <li class="lang-site"><a href="lang.php?l=ar" class="page-scroll"><span>AR</span></a></li>
                <?php endif; ?>
            </ul>
            <div class="opt-mobail">
                <?php if ($lang === 'ar'): ?>
                <li class="lang-site"><a href="lang.php?l=en" class="page-scroll"><span>EN</span></a></li>
                <?php else: ?>
                <li class="lang-site"><a href="lang.php?l=ar" class="page-scroll"><span>AR</span></a></li>
                <?php endif; ?>
                <button type="button" class="hamburger">
                    <span class="hamb-top"></span>
                    <span class="hamb-middle"></span>
                    <span class="hamb-bottom"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="section_home">
        <div class="container">
            <div class="thumb-home">
                <a href=""><img src="<?= $s['home_image'] ? e(imgUrl($s['home_image'])) : 'assets/images/thumb-home.jpg' ?>" alt=""></a>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section class="section_products" id="products">
        <div class="container">
            <div class="sec_head wow fadeInUp">
                <h2><?= lbl('Our products') ?></h2>
                <p><?= e($s[col('product_description')] ?? '') ?></p>
            </div>
            <?php if (count($products) > 5): ?>
            <div class="pslider-wrap">
                <button class="pslider-btn pslider-btn-right" id="prodBtnRight"><i class="fas fa-chevron-right"></i></button>
                <div class="pslider-vp" id="prodVp">
                    <div class="pslider-track" id="prodTrack">
                        <?php foreach ($products as $p): ?>
                        <div class="pslider-item prod-item">
                            <div class="item-product">
                                <a href="<?= $p['url'] ? e($p['url']) : '#' ?>">
                                <figure>
                                    <?php if ($p['image']): ?>
                                    <img src="<?= e(imgUrl($p['image'])) ?>" alt="<?= e(val($p, 'name')) ?>">
                                    <?php else: ?>
                                    <img src="assets/images/GREEN_SPROUT.svg" alt="">
                                    <?php endif; ?>
                                </figure>
                                </a>
                                <div class="txt-product">
                                    <h4><?php if ($p['url']): ?><a href="<?= e($p['url']) ?>"><?= e(val($p, 'name')) ?></a><?php else: ?><a href="#"><?= e(val($p, 'name')) ?></a><?php endif; ?></h4>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="pslider-btn pslider-btn-left" id="prodBtnLeft"><i class="fas fa-chevron-left"></i></button>
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($products as $p): ?>
                <div class="col--5">
                    <div class="item-product wow fadeInUp">
                        <a href="<?= $p['url'] ? e($p['url']) : '#' ?>">
                        <figure>
                            <?php if ($p['image']): ?>
                            <img src="<?= e(imgUrl($p['image'])) ?>" alt="<?= e(val($p, 'name')) ?>">
                            <?php else: ?>
                            <img src="assets/images/GREEN_SPROUT.svg" alt="">
                            <?php endif; ?>
                        </figure>
                        </a>
                        <div class="txt-product">
                            <h4><?php if ($p['url']): ?><a href="<?= e($p['url']) ?>"><?= e(val($p, 'name')) ?></a><?php else: ?><a href="#"><?= e(val($p, 'name')) ?></a><?php endif; ?></h4>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Statistics -->
    <section class="section_statistics">
        <div class="container">
            <div class="sec_head wow fadeInUp">
                <h2><?= lbl('Numbers Target') ?></h2>
                <p><?= e($s[col('numbers_description')] ?? '') ?></p>
            </div>
            <div class="row">
                <?php foreach ($numbers as $n): ?>
                <div class="col--5">
                    <div class="item-statistics wow fadeInUp">
                        <h2 class="count-number count"><?= e($n['number']) ?></h2>
                        <p><?= e(val($n, 'description')) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- About -->
    <section class="section_about" id="about-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="cont-about wow fadeInUp">
                        <h4><?= lbl('About the company') ?></h4>
                        <p><?= e($s[col('about')] ?? '') ?></p>
                        <figure>
                            <img src="<?= $s['about_image'] ? e(imgUrl($s['about_image'])) : 'assets/images/thumb-about.jpg' ?>" alt="Image About">
                        </figure>
                        <?php
                        $btnText = $lang === 'ar' ? ($s['about_button_text_ar'] ?? '') : ($s['about_button_text_en'] ?? '');
                        $btnUrl  = $s['about_button_url'] ?? '';
                        if ($btnText && $btnUrl):
                        ?>
                        <div style="margin-top:20px;">
                            <a href="<?= e($btnUrl) ?>" class="about-cta-btn" target="_blank" rel="noopener noreferrer">
                                <?= e($btnText) ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="other-about wow fadeInUp">
                        <div class="cont-vision">
                            <h4><?= lbl('Our vision') ?></h4>
                            <p><?= e($s[col('vision')] ?? '') ?></p>
                        </div>
                        <div class="cont-message">
                            <h4><?= lbl('Message we believe in') ?></h4>
                            <p><?= e($s[col('message')] ?? '') ?></p>
                            <figure><img src="assets/images/thumb-msg.svg" alt=""></figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="section_our_team">
        <div class="container">
            <div class="sec_head wow fadeInUp">
                <h2><?= lbl('Our team') ?></h2>
                <p><?= e($s[col('team_description')] ?? '') ?></p>
            </div>
            <?php if (count($teams) > 4): ?>
            <div class="pslider-wrap">
                <button class="pslider-btn pslider-btn-right" id="teamBtnRight"><i class="fas fa-chevron-right"></i></button>
                <div class="pslider-vp" id="teamVp">
                    <div class="pslider-track" id="teamTrack">
                        <?php foreach ($teams as $tm): ?>
                        <div class="pslider-item team-item">
                            <div class="item-team">
                                <div class="txt-team">
                                    <h5><?= e(val($tm, 'name')) ?></h5>
                                    <p><?= e(val($tm, 'description')) ?></p>
                                    <span><?= e($tm['mobile'] ?? '') ?></span>
                                    <a href="mailto:<?= e($tm['email'] ?? '') ?>"><?= e($tm['email'] ?? '') ?></a>
                                </div>
                                <figure>
                                    <img src="<?= $tm['image'] ? e(imgUrl($tm['image'])) : 'assets/images/t1.png' ?>" alt="team">
                                </figure>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="pslider-btn pslider-btn-left" id="teamBtnLeft"><i class="fas fa-chevron-left"></i></button>
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($teams as $tm): ?>
                <div class="col-lg-3">
                    <div class="item-team">
                        <div class="txt-team">
                            <h5><?= e(val($tm, 'name')) ?></h5>
                            <p><?= e(val($tm, 'description')) ?></p>
                            <span><?= e($tm['mobile'] ?? '') ?></span>
                            <a href="mailto:<?= e($tm['email'] ?? '') ?>"><?= e($tm['email'] ?? '') ?></a>
                        </div>
                        <figure>
                            <img src="<?= $tm['image'] ? e(imgUrl($tm['image'])) : 'assets/images/t1.png' ?>" alt="team">
                        </figure>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Partners -->
    <section class="section_clients" id="partners">
        <div class="container">
            <div class="sec_head wow fadeInUp">
                <h2><?= lbl('Proud of being in partnership with ') ?></h2>
            </div>
            <?php
            $pts = $partners ? $partners : array();
            if (empty($pts)) {
                for ($i = 1; $i <= 9; $i++) {
                    $pts[] = array('image' => 'assets/images/c' . $i . '.png', 'name' => '');
                }
            }
            ?>
            <div class="pslider-wrap">
                <button class="pslider-btn pslider-btn-right" id="pBtnRight" aria-label="prev"><i class="fas fa-chevron-right"></i></button>
                <div class="pslider-vp" id="pVp">
                    <div class="pslider-track" id="pTrack">
                        <?php foreach ($pts as $pt):
                            $img = $pt['image'];
                            if (substr($img, 0, 4) !== 'http') $img = ltrim($img, '/');
                        ?>
                        <div class="pslider-item">
                            <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($pt['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="pslider-btn pslider-btn-left" id="pBtnLeft" aria-label="next"><i class="fas fa-chevron-left"></i></button>
            </div>
        </div>
    </section>

    <!-- Branches -->
    <section class="section_branches" id="branches">
        <div class="container">
            <div class="txt-branches">
                <h5><?= lbl('Our branches around the Kingdom') ?></h5>
                <?php foreach ($branches as $i => $br): ?>
                <p class="branch-item"
                   data-name="<?= e(val($br, 'name')) ?>"
                   data-phone="<?= e($br['phone'] ?? '') ?>"
                   data-address="<?= e($lang === 'ar' ? ($br['address_ar'] ?? '') : ($br['address_en'] ?? '')) ?>"
                   data-hours="<?= e($lang === 'ar' ? ($br['working_hours_ar'] ?? '') : ($br['working_hours_en'] ?? '')) ?>"
                   data-map="<?= e($br['map_url'] ?? '') ?>">
                    <?= ($i + 1) . '. ' . e(val($br, 'name')) ?>
                </p>
                <?php endforeach; ?>
            </div>
            <figure>
                <img src="<?= $s['branch_image'] ? e(imgUrl($s['branch_image'])) : 'assets/images/map.svg' ?>" alt="">
            </figure>
        </div>
    </section>

    <!-- Branch Modal -->
    <div class="modal fade" id="branchModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content branch-modal-content">
                <div class="modal-header branch-modal-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <h5 class="modal-title branch-modal-title" id="bmName"></h5>
                    <button type="button" class="branch-modal-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <div class="branch-detail-row" id="bmPhoneRow">
                        <span class="branch-detail-icon"><img src="assets/images/icons-telephone.svg" style="width:20px"></span>
                        <span id="bmPhone"></span>
                    </div>
                    <div class="branch-detail-row" id="bmHoursRow">
                        <span class="branch-detail-icon"><i class="fas fa-clock" style="font-size:18px;color:#35285C;"></i></span>
                        <span id="bmHours"></span>
                    </div>
                    <div class="branch-detail-row" id="bmAddressRow">
                        <span class="branch-detail-icon"><img src="assets/images/icon-location.svg" style="width:20px"></span>
                        <span id="bmAddress"></span>
                    </div>
                </div>
                <div class="modal-footer branch-modal-footer" id="bmMapRow">
                    <a id="bmMapBtn" href="#" target="_blank" class="branch-map-btn">
                        <?= lbl('Get Directions') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer id="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="dta-footer">
                        <div class="cont-ft wow fadeInUp">
                            <figure class="logo-ft wow fadeInUp">
                                <img src="<?= $s['logo'] ? e(imgUrl($s['logo'])) : 'assets/images/logo.svg' ?>" alt="Logo" class="img-fluid">
                            </figure>
                            <p><?= e($s[col('description')] ?? '') ?></p>
                        </div>
                        <ul class="list-contact wow fadeInUp">
                            <li>
                                <figure><img src="assets/images/icon-location.svg" alt=""></figure>
                                <p><?= e($lang === 'ar' ? ($s['address'] ?? '') : ($s['address_en'] ?? $s['address'] ?? '')) ?></p>
                            </li>
                            <li>
                                <figure><img src="assets/images/mailbox.svg" alt=""></figure>
                                <p><?= lbl('Mail box') ?> : <?= e($lang === 'ar' ? ($s['post_address'] ?? '') : ($s['post_address_en'] ?? $s['post_address'] ?? '')) ?></p>
                            </li>
                            <li>
                                <figure><img src="assets/images/icons-telephone.svg" alt=""></figure>
                                <p><?= lbl('Phone') ?> <?= e($s['phone'] ?? '') ?></p>
                            </li>
                            <li>
                                <figure><img src="assets/images/icon-phone.svg" alt=""></figure>
                                <p><?= lbl('Mobile') ?> <?= e($s['mobile'] ?? '') ?></p>
                            </li>
                            <li>
                                <figure><img src="assets/images/icon-email.svg" alt=""></figure>
                                <p><?= lbl('Email') ?> : <?= e($s['email'] ?? '') ?></p>
                            </li>
                            <li>
                                <figure><img src="assets/images/compass.svg" alt=""></figure>
                                <p><?= lbl('Website') ?> : <?= e($s['website'] ?? '') ?></p>
                            </li>
                        </ul>
                        <div class="thumb-footer">
                            <figure><img src="assets/images/thumb-footer.png" alt=""></figure>
                            <p class="copyRight wow fadeInUp"><?= lbl('All rights reserved') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="cont-contact">
                        <h5><?= lbl('Contact us') ?></h5>
                        <?php if ($flash): ?>
                        <div class="<?= $flash['type'] === 'success' ? 'alert-success-custom' : 'alert-error-custom' ?>">
                            <?= e($flash['msg']) ?>
                        </div>
                        <?php endif; ?>
                        <form class="form-contact" action="connect_us.php" method="post">
                            <div class="form-group">
                                <label><?= lbl('Name') ?></label>
                                <input type="text" name="name" class="form-control" placeholder="<?= lbl('Name') ?>" required>
                            </div>
                            <div class="form-group">
                                <label><?= lbl('E-mail') ?></label>
                                <input type="email" name="email" class="form-control" placeholder="<?= lbl('E-mail') ?>" required>
                            </div>
                            <div class="form-group">
                                <label><?= lbl('Message text') ?></label>
                                <textarea name="text" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <button class="btn-site" type="submit"><span><?= lbl('send') ?></span></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</div><!-- main-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/all.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/wow.js"></script>
<script src="assets/js/jquery.easing.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
new WOW().init();

$(document).on('click', '.branch-item', function () {
    var n = $(this).data('name'), ph = $(this).data('phone'),
        addr = $(this).data('address'), hrs = $(this).data('hours'), map = $(this).data('map');
    $('#bmName').text(n);
    ph   ? ($('#bmPhone').text(ph), $('#bmPhoneRow').show())     : $('#bmPhoneRow').hide();
    hrs  ? ($('#bmHours').text(hrs), $('#bmHoursRow').show())    : $('#bmHoursRow').hide();
    addr ? ($('#bmAddress').text(addr), $('#bmAddressRow').show()): $('#bmAddressRow').hide();
    map  ? ($('#bmMapBtn').attr('href', map), $('#bmMapRow').show()): $('#bmMapRow').hide();
    $('#branchModal').modal('show');
});

// Smooth scroll for .page-scroll links with hash hrefs
$('.page-scroll[href^="#"]').on('click', function(e) {
    var target = $(this).attr('href');
    if (target.length > 1 && $(target).length) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: $(target).offset().top - 80 }, 800, 'swing');
    }
});
</script>
<?php if (!empty($s['body_code'])) echo $s['body_code']; ?>
<script>
(function() {
    var isRTL = document.documentElement.dir === 'rtl';

    function initSlider(vpId, trackId, btnRId, btnLId, ppFn) {
        var vp    = document.getElementById(vpId);
        var track = document.getElementById(trackId);
        if (!track || !vp) return;
        var n    = track.querySelectorAll('.pslider-item').length;
        var idx  = 0;
        var auto;
        var btnR = document.getElementById(btnRId);
        var btnL = document.getElementById(btnLId);

        function maxIdx() { return Math.max(0, n - ppFn()); }

        function update() {
            var pp     = ppFn();
            var itemW  = vp.offsetWidth / pp;
            var offset = idx * itemW;
            track.style.transform = isRTL
                ? 'translateX(' + offset + 'px)'
                : 'translateX(-' + offset + 'px)';
            var multi = n > pp;
            if (btnR) btnR.style.display = multi ? '' : 'none';
            if (btnL) btnL.style.display = multi ? '' : 'none';
        }

        function go(step) {
            idx += step;
            if (idx > maxIdx()) idx = 0;
            if (idx < 0)        idx = maxIdx();
            update();
            clearInterval(auto);
            auto = setInterval(function() { go(1); }, 4500);
        }

        if (btnR) btnR.onclick = function() { go(isRTL ? -1 : 1); };
        if (btnL) btnL.onclick = function() { go(isRTL ?  1 : -1); };
        window.addEventListener('resize', function() { idx = 0; update(); });
        update();
        auto = setInterval(function() { go(1); }, 4500);
    }

    // Partners
    initSlider('pVp', 'pTrack', 'pBtnRight', 'pBtnLeft', function() {
        return window.innerWidth >= 992 ? 6 : window.innerWidth >= 576 ? 4 : 2;
    });
    // Products (only rendered when > 5 items)
    initSlider('prodVp', 'prodTrack', 'prodBtnRight', 'prodBtnLeft', function() {
        return window.innerWidth >= 992 ? 5 : window.innerWidth >= 576 ? 3 : 2;
    });
    // Team (only rendered when > 4 members)
    initSlider('teamVp', 'teamTrack', 'teamBtnRight', 'teamBtnLeft', function() {
        return window.innerWidth >= 992 ? 4 : window.innerWidth >= 576 ? 2 : 1;
    });
})();
</script>
</body>
</html>
