<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('article_cate_id')->unsigned()->comment('分类ID');
            $table->string('title')->comment('标题');
            $table->text('brief')->nullable()->comment('文章简介');
            $table->tinyInteger('mode')->default(0)->comment('0在线编辑器 1自定义代码文章');
            $table->string('img')->nullable()->comment('文章主图');
            $table->string('img_alt')->nullable();
            $table->integer('read_num')->default(0)->comment('阅读数');
            $table->integer('real_read_num')->default(0)->comment('真实阅读数');
            $table->text('content')->nullable()->comment('内容');
            $table->string('html_file')->nullable()->comment('自定义布局html');
            $table->integer('sort')->default(1)->comment('排序');
            $table->string('stars')->default(5)->comment('星星');
            $table->tinyInteger('status')->default(0)->comment('0草稿 1正常');
            $table->tinyInteger('is_recommend')->default(0)->comment('是否推荐');
            $table->tinyInteger('is_top')->default(0)->comment('是否置顶');
            $table->text('seo_title')->nullable();
            $table->text('seo_keyword')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('release_at')->useCurrent()->useCurrentOnUpdate()->comment('发布时间');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
