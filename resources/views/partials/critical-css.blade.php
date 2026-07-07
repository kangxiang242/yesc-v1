<style>
/* Critical CSS for above-the-fold content - 与原始 float 布局保持一致，避免冲突 */

/* Header - 使用原始 float 布局，不使用 flex */
.header-back {
    background: var(--main-color, #0D8DD3);
    width: 100%;
    height: 90px;
    position: absolute;
    z-index: 0;
    overflow: hidden;
    border-bottom: 10px solid #0E79BE;
}

.header-main {
    width: 1200px;
    margin: 0 auto;
    padding-top: 10px;
    padding-bottom: 10px;
    position: relative;
    z-index: 10;
}

.header-logo {
    float: left;
    width: 256px;
    opacity: 1;
}

.header-nav {
    float: right;
}

.header-conceal {
    color: #fff;
    font-size: 12px;
    text-align: right;
    margin-bottom: 14px;
}

/* Navigation - 使用原始 float 布局 */
.nav-main {
    float: left;
}

.header-nav .nav-item {
    float: left;
    font-size: 14px;
    text-align: center;
}

.header-nav .nav-item a {
    color: #fff;
    font-size: 14px;
}

.header-nav .nav-item:after {
    content: "/";
    margin-left: 14px;
    margin-right: 14px;
    color: #fff;
}

.header-nav .nav-item:last-child:after,
.header-nav .nav-item:nth-last-child(2):after {
    content: "";
    margin-right: 0;
}

/* Banner */
.banner-container {
    width: 100%;
    height: 400px;
    overflow: hidden;
    position: relative;
}

.banner-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Content layout - 使用原始 float 布局 */
.wrapper {
    width: 1200px;
    margin: 0 auto;
}

/* Article */
.article-main {
    background: white;
    padding: 20px;
    border-radius: 8px;
}

.article-content {
    line-height: 1.6;
    color: #333;
}

/* Product */
.images-box {
    width: 320px;
}

.main-img {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

/* Footer - 使用原始布局 */
.footer-backage {
    background: #2c3e50;
    width: 100%;
    position: absolute;
    top: 0;
    height: 790px;
}

.footer-main {
    width: 1200px;
    margin: 0 auto;
    position: relative;
}

.footer-nav-item {
    float: left;
    margin-right: 55px;
    align-self: center;
    font-size: 14px;
}

.footer-nav-item a {
    color: #fff;
}

/* Mobile specific - 移动端可使用 flex */
@media (max-width: 768px) {
    .header-back {
        height: 60px;
    }

    .header-main {
        padding: 15px;
    }

    .header-logo {
        height: 30px;
    }

    .nav-main {
        gap: 10px;
    }

    .nav-item a {
        padding: 6px 12px;
        font-size: 14px;
    }

    .banner-container {
        height: 200px;
    }

    .footer-main {
        flex-direction: column;
        gap: 20px;
    }
}
</style>
