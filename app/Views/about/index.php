<?php
$title = trans('content.about.page_title', 'About Us');
ob_start();
$aboutMissionVideo = asset('uploads/media/video/2026/03/46b910cd7e6c35205bcc990eed4d9662.mp4');
$aboutMissionPoster = asset('images/about-team.avif');
?>

<section class="relative py-14 md:py-16 px-4 overflow-hidden" style="<?php echo innerHeroBackgroundStyle(); ?>">
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22><circle cx=%2230%22 cy=%2230%22 r=%222%22 fill=%22%23C8A951%22/></svg>');"></div>
    <div class="absolute top-8 right-16 w-24 h-24 rounded-full opacity-15 animate-float" style="background: radial-gradient(circle, var(--theme-accent) 0%, transparent 70%);"></div>

    <div class="max-w-5xl mx-auto text-center relative z-10" data-aos="fade-up">
        <span class="inline-block px-4 py-2 rounded-full mb-5 text-xs font-semibold tracking-widest uppercase" style="background-color: color-mix(in srgb, var(--theme-accent) 20%, transparent); color: var(--theme-accent); font-family: var(--font-ui); letter-spacing: 0.2em;">
            <?php echo htmlspecialchars(trans('content.about.hero.badge', 'About Sapphire Events')); ?>
        </span>
        <h1 class="text-4xl md:text-5xl font-light mb-4 leading-tight text-white" style="font-family: var(--font-display); letter-spacing: -0.02em;">
            <?php echo htmlspecialchars(trans('content.about.hero.title', 'Crafting Celebrations with Purpose')); ?>
        </h1>
        <p class="text-base md:text-lg text-gray-300 max-w-3xl mx-auto leading-relaxed" style="font-family: var(--font-ui);">
            <?php echo htmlspecialchars(trans('content.about.hero.description', 'We blend design excellence, operational precision, and human-centered service to deliver events that are beautiful, seamless, and memorable.')); ?>
        </p>
    </div>
</section>

<section class="page-deferred-section pt-20 pb-10 px-4" style="background-color: #F8F5F2;">
    <div class="w-full">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block px-4 py-2 rounded-full mb-4 text-xs font-semibold tracking-widest uppercase" style="background-color: rgba(15, 61, 62, 0.1); color: #C8A951; font-family: 'Montserrat', sans-serif; letter-spacing: 0.18em;">
                <?php echo htmlspecialchars(trans('content.about.team.badge', 'The Full Story')); ?>
            </span>
            <h2 class="text-3xl md:text-3xl font-light mb-4" style="color: #0F3D3E; letter-spacing: -0.02em;">
                <?php echo htmlspecialchars(trans('content.about.team.title', 'People Behind the Experience')); ?>
            </h2>
            <p class="text-base text-gray-600 max-w-5xl mx-auto">
                <?php echo htmlspecialchars(trans('content.about.team.description', 'A multidisciplinary team focused on design excellence, flawless coordination, and high-touch client support.')); ?>
            </p>
        </div>
        <div class="space-y-8 lg:space-y-10">
            <div class="about-story-row" data-aos="fade-up">
                <figure class="about-story-media">
                    <video
                        class="about-story-image about-story-image--mission about-mission-video"
                        muted
                        loop
                        playsinline
                        preload="none"
                        poster="<?php echo htmlspecialchars($aboutMissionPoster); ?>"
                        data-src="<?php echo htmlspecialchars($aboutMissionVideo); ?>"
                        aria-label="<?php echo htmlspecialchars(trans('content.about.mission_vision.mission_image_alt', 'Our mission and vision')); ?>">
                    </video>
                </figure>
                <article class="about-feature-card about-story-card pt-10 md:pt-12">
                    <div class="about-icon-wrap" aria-hidden="true"><i class="fas fa-bullseye"></i></div>
                    <h3 class="text-center text-3xl font-bold mb-3" style="color: #0F3D3E;">
                        <?php echo htmlspecialchars(trans('content.about.mission_vision.mission_title', 'Mission')); ?>
                    </h3>
                    <p class="text-gray-600 text-lg leading-relaxed pt-8  md:px-8 line-spacing-1.6">
                        <?php echo htmlspecialchars(trans('content.about.mission_vision.mission_desc', 'At Sapphire Events & Decorations, our mission is to transform your special occasions into unforgettable experiences. With meticulous attention to detail and a passion for creativity, we strive to exceed your expectations, delivering exceptional event planning and stunning decorations that bring your vision to life.')); ?>
                    </p>
                    <p class="text-gray-600 text-lg leading-relaxed mt-4 md:px-8 line-spacing-1.6">Let us make your moments shine with elegance and sophistication.</p>
                </article>
            </div>
            <div class="about-story-row about-story-row--reverse" data-aos="fade-up" data-aos-delay="100">
                <figure class="about-story-media">
                    <img src="<?php echo asset('images/about-team.avif'); ?>" alt="<?php echo htmlspecialchars(trans('content.about.team.team_image_alt', 'Our team')); ?>" class="about-story-image about-story-image--top about-story-image--vision" loading="lazy" decoding="async">
                </figure>
                <article class="about-feature-card about-story-card pt-10 md:pt-12">
                    <div class="about-icon-wrap" aria-hidden="true"><i class="fas fa-eye"></i></div>
                    <h3 class="text-center text-3xl font-bold mb-3" style="color: #0F3D3E;">
                        <?php echo htmlspecialchars(trans('content.about.mission_vision.vision_title', 'Vision')); ?>
                    </h3>
                    <p class="text-gray-600 text-lg leading-relaxed pt-8 md:px-8 line-spacing-1.6">
                        <?php echo htmlspecialchars(trans('content.about.mission_vision.vision_desc', 'Our vision at Sapphire Events & Decorations is to be the first choice for creating magical moments that last a lifetime. We aim to inspire and delight our clients with innovative designs, impeccable service, and a commitment to excellence in every event we undertake.')); ?>
                    </p>
                    <p class="text-gray-600 text-lg leading-relaxed mt-4 md:px-8"> With our expertise and dedication, we envision turning dreams into reality, one celebration at a time.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="page-deferred-section py-20 px-4" style="background-color: #fff;">
    <div class="w-full">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block px-4 py-2 rounded-full mb-4 text-xs font-semibold tracking-widest uppercase" style="background-color: rgba(15, 61, 62, 0.1); color: #C8A951; font-family: 'Montserrat', sans-serif; letter-spacing: 0.18em;">
                <?php echo htmlspecialchars(trans('content.about.gallery.badge', 'Visual Highlights')); ?>
            </span>
            <h2 class="text-3xl md:text-4xl font-light mb-4" style="color: #0F3D3E; font-family: 'Cormorant Garamond', serif; letter-spacing: -0.02em;">
                Our Signature Aesthetic
            </h2>
        </div>

        <div class="flex flex-wrap justify-center gap-4 md:gap-5" id="about-highlights-grid">
            <?php
            $getGalleryMediaUrl = static function (?string $media): string {
                if (!$media) {
                    return '';
                }
                if (preg_match('/^https?:\/\//', $media)) {
                    return $media;
                }
                return uploadedImageUrl($media);
            };

            $galleryItems = $highlightImages ?? [];
            
            if (!empty($galleryItems)):
                foreach ($galleryItems as $index => $item):
                    $mediaUrl = $getGalleryMediaUrl($item['image'] ?? null);
            ?>
                <div class="gallery-item justified-gallery-item rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300" data-aos="fade-up" data-aos-delay="<?php echo ($index % 4) * 50; ?>">
                    <div class="relative h-full overflow-hidden bg-gray-200">
                        <?php if (!empty($mediaUrl)): ?>
                            <img
                                src="<?php echo htmlspecialchars($mediaUrl); ?>"
                                alt="<?php echo htmlspecialchars($item['title'] ?? 'Gallery item'); ?>"
                                class="h-full w-auto block hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                                decoding="async"
                            >
                        <?php else: ?>
                            <div class="h-full flex items-center justify-center bg-gradient-to-br from-gray-300 to-gray-400" style="width: 12rem;">
                                <i class="fas fa-image text-gray-500 text-3xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-black/0 hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                            <a href="<?php echo route('/gallery'); ?>" class="opacity-0 hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-expand text-white text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach;
            else:
            ?>
                <p class="col-span-full text-center text-gray-500 py-12"><?php echo htmlspecialchars(trans('content.about.gallery.empty', 'Gallery images will appear here soon.')); ?></p>
            <?php 
            endif;
            ?>
        </div>

        <div class="text-center mt-12" data-aos="fade-up">
            <a href="<?php echo route('/gallery'); ?>" class="inline-flex items-center justify-center px-7 py-3 rounded-lg font-semibold transition-all duration-300 hover:shadow-lg" style="background-color: #0F3D3E; color: #fff; font-family: 'Montserrat', sans-serif; letter-spacing: 0.08em; text-transform: uppercase; font-size: 0.8rem;">
                <?php echo htmlspecialchars(trans('content.about.gallery.view_all', 'View Full Gallery')); ?> <i class="fas fa-arrow-right ml-2" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const missionVideo = document.querySelector('.about-mission-video');

        if (missionVideo && !reduceMotion) {
            const activateMissionVideo = () => {
                if (missionVideo.dataset.loaded === 'true') {
                    return;
                }

                const source = missionVideo.dataset.src;
                if (!source) {
                    return;
                }

                missionVideo.dataset.loaded = 'true';
                missionVideo.innerHTML = '<source src="' + source + '" type="video/mp4">';
                missionVideo.load();
                missionVideo.play().catch(() => {});
            };

            if ('IntersectionObserver' in window) {
                const videoObserver = new IntersectionObserver((entries) => {
                    if (!entries[0] || !entries[0].isIntersecting) {
                        return;
                    }

                    activateMissionVideo();
                    videoObserver.disconnect();
                }, { rootMargin: '200px 0px' });

                videoObserver.observe(missionVideo);
            } else {
                activateMissionVideo();
            }
        }

        // --- Justified gallery: rows fill the container width exactly with
        // no cropping, by scaling each row's height so its images' natural
        // aspect ratios sum to the available width. ---
        const justifyGrid = document.getElementById('about-highlights-grid');
        if (justifyGrid) {
            const items = Array.from(justifyGrid.querySelectorAll('.justified-gallery-item'));
            const images = items
                .map((item) => item.querySelector('img'))
                .filter((img) => img);

            const getGap = () => parseFloat(getComputedStyle(justifyGrid).columnGap) || 0;
            const getTargetHeight = () => parseFloat(getComputedStyle(items[0]).height) || 260;

            const runJustify = () => {
                if (!images.every((img) => img.complete && img.naturalWidth > 0)) {
                    return;
                }

                const containerWidth = justifyGrid.clientWidth;
                if (!containerWidth) {
                    return;
                }

                const gap = getGap();
                const targetHeight = getTargetHeight();
                const maxHeight = targetHeight * 1.5;

                let row = [];
                let rowNaturalWidth = 0;

                const flushRow = (isLastRow) => {
                    if (!row.length) {
                        return;
                    }

                    const gapsWidth = gap * (row.length - 1);
                    const scale = (containerWidth - gapsWidth) / (rowNaturalWidth - gapsWidth);
                    let rowHeight = targetHeight * scale;

                    if (isLastRow && rowHeight > maxHeight) {
                        rowHeight = targetHeight;
                    }

                    row.forEach(({ img, ratio }) => {
                        img.style.height = rowHeight + 'px';
                        img.style.width = (ratio * rowHeight) + 'px';
                    });

                    row = [];
                    rowNaturalWidth = 0;
                };

                images.forEach((img) => {
                    const ratio = img.naturalWidth / img.naturalHeight;
                    const naturalWidth = ratio * targetHeight;

                    if (row.length && rowNaturalWidth + gap + naturalWidth > containerWidth) {
                        flushRow(false);
                    }

                    row.push({ img, ratio });
                    rowNaturalWidth += (row.length > 1 ? gap : 0) + naturalWidth;
                });

                flushRow(true);
            };

            const debouncedJustify = (() => {
                let handle = null;
                return () => {
                    if (handle) {
                        window.cancelAnimationFrame(handle);
                    }
                    handle = window.requestAnimationFrame(runJustify);
                };
            })();

            images.forEach((img) => {
                if (img.complete) {
                    debouncedJustify();
                } else {
                    img.addEventListener('load', debouncedJustify);
                }
            });

            window.addEventListener('resize', debouncedJustify);

            if ('IntersectionObserver' in window) {
                const gridObserver = new IntersectionObserver((entries) => {
                    if (entries[0] && entries[0].isIntersecting) {
                        debouncedJustify();
                        gridObserver.disconnect();
                    }
                }, { rootMargin: '400px 0px' });
                gridObserver.observe(justifyGrid);
            }
        }
    });
</script>

<style>
    .page-deferred-section {
        content-visibility: auto;
        contain-intrinsic-size: 1px 900px;
    }

    .about-feature-card {
        background: #fff;
        border-radius: 1rem;
        padding: 1.6rem;
        transition: transform 0.35s ease, box-shadow 0.35s ease;
    }

    .about-feature-card:hover {
        transform: translateY(-6px);
    }

    .about-story-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1.5rem;
        align-items: stretch;
    }

    .about-story-media {
        margin: 0;
        min-height: 100%;
        overflow: hidden;
    }

    .about-story-image {
        width: 100%;
        height: 100%;
        min-height: 250px;
        max-height: 440px;
        object-fit: cover;
        display: block;
    }

    .about-story-image--top {
        object-position: top center;
    }

    .about-story-image--vision {
        min-height: 400px;
        max-height: 620px;
    }

    .about-story-image--mission {
        min-height: 400px;
        max-height: 620px;
    }

    .about-story-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 100%;
        align-items: center;
        text-align: center;
        overflow: hidden;
    }

    .about-story-card > h3,
    .about-story-card > p {
        width: 100%;
        max-width: 34rem;
        overflow-wrap: anywhere;
    }

    .about-icon-wrap {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 1.2rem;
        color: #C8A951;
        background: linear-gradient(135deg, rgba(15, 61, 62, 0.1), rgba(200, 169, 81, 0.14));
    }

    .line-clamp-5 {
        display: -webkit-box;
        -webkit-line-clamp: 5;
        line-clamp: 5;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .gallery-item {
        transition: transform 0.35s ease, box-shadow 0.35s ease;
    }

    .justified-gallery-item {
        height: 220px;
    }

    @media (min-width: 640px) {
        .justified-gallery-item {
            height: 250px;
        }
    }

    @media (min-width: 1024px) {
        .justified-gallery-item {
            height: 280px;
        }
    }

    .gallery-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(15, 61, 62, 0.15);
    }

    .gallery-item img {
        transition: transform 0.35s ease;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(3deg); }
    }

    @keyframes float-delayed {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(-3deg); }
    }

    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    .animate-float-delayed {
        animation: float-delayed 8s ease-in-out infinite;
        animation-delay: 2s;
    }

    @media (prefers-reduced-motion: reduce) {
        .about-feature-card {
            transition: none;
        }

        .animate-float,
        .animate-float-delayed {
            animation: none;
        }
    }

    @media (min-width: 1024px) {
        .about-story-row {
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
            gap: 2rem;
        }

        .about-story-row--reverse .about-story-media {
            order: 2;
        }

        .about-story-row--reverse .about-story-card {
            order: 1;
        }

        .about-story-image {
            min-height: 280px;
            max-height: 340px;
        }

        .about-story-image--vision {
            min-height: 400px;
            max-height: 620px;
        }

        .about-story-image--mission {
            min-height: 400px;
            max-height: 620px;
        }
    }
</style>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/app.php';
?>
