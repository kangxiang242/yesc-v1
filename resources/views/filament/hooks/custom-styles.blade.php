<style>
    /* Compact table cells */
    .fi-ta-cell {
        padding-block: 0.5rem !important;
    }

    .fi-ta-header-cell {
        padding-block: 0.5rem !important;
        font-size: 0.75rem !important;
    }

    /* Smaller text in table body */
    .fi-ta-cell .fi-ta-col-text {
        font-size: 0.8125rem !important;
        line-height: 1.25rem !important;
    }

    /* Compact status/select columns */
    .fi-ta-cell .fi-fo-select {
        min-width: 100px !important;
    }

    .fi-ta-cell .fi-fo-select .fi-input-wrp {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.75rem !important;
    }

    /* Action buttons always visible and compact */
    .fi-ta-actions {
        flex-shrink: 0 !important;
        position: sticky !important;
        right: 0 !important;
        background: white !important;
        z-index: 10 !important;
        border-left: 1px solid #e5e7eb !important;
        padding-left: 0.75rem !important;
    }

    .dark .fi-ta-actions {
        background: #1f2937 !important;
        border-left-color: #374151 !important;
    }

    /* Compact description text */
    .fi-ta-col-text .fi-badge {
        font-size: 0.6875rem !important;
        padding: 0.125rem 0.375rem !important;
    }

    /* Table row hover */
    .fi-ta-row:hover {
        background-color: rgba(99, 102, 241, 0.04) !important;
    }

    /* Smaller pagination */
    .fi-pagination {
        padding-block: 0.5rem !important;
    }

    /* Sidebar width optimization - narrower sidebar, wider content */
    .fi-sidebar {
        width: 10.5rem !important;
    }

    /* 右侧内容区左右边距加大 */
    .fi-main {
        padding-left: 3rem !important;
        padding-right: 3rem !important;
    }

    .fi-sidebar .fi-brand-logo-text {
        font-size: 0.8125rem !important;
    }

    .fi-sidebar .fi-sidebar-nav-item-label {
        font-size: 0.75rem !important;
    }

    .fi-sidebar .fi-icon {
        width: 1.125rem !important;
        height: 1.125rem !important;
    }

    .fi-sidebar .fi-sidebar-nav > ul > li {
        padding-inline: 0.25rem !important;
    }

    .fi-sidebar .fi-sidebar-nav-item {
        gap: 0.375rem !important;
        padding-block: 0.3125rem !important;
        padding-inline: 0.375rem !important;
    }

    /* Table container scroll */
    .fi-ta-content {
        overflow-x: auto !important;
    }

    /* Tooltip styling */
    .fi-ta-col-text [x-data*="tooltip"] {
        cursor: help;
    }

    /* 表格紧凑布局 */
    .fi-ta-content {
        overflow-x: auto !important;
    }

    .fi-ta-table th,
    .fi-ta-table td {
        white-space: normal !important;
        overflow-wrap: break-word !important;
        text-align: left !important;
    }

    /* 收货人信息列：让 p 块级元素正常换行 */
    .fi-table-cell-name .fi-ta-text-item {
        display: block !important;
    }

    /* 操作列不固定 */
    .fi-ta-actions {
        position: static !important;
        border-left: none !important;
    }

    /* 订单页 toolbar 上的刷新/导出按钮 — 默认隐藏；只在订单页才显示 */
    .fi-ta-header-toolbar > .order-toolbar-buttons { display: none !important; }
    .fi-resource-orders .fi-ta-header-toolbar > .order-toolbar-buttons { display: flex !important; align-items: center; gap: 0.5rem; }
    /* 圖片列表：原圖顯示、不裁剪 */
	.fi-ta-image img {
	    border-radius: 0 !important;
	    object-fit: contain !important;
	    max-height: 8rem !important;
	    width: auto !important;
	}

	/* FileUpload（FilePond）圖片預覽：移除黑底 */
	.filepond--image-preview {
	    background-color: transparent !important;
	}
	.filepond--image-preview-overlay-idle {
	    mix-blend-mode: normal !important;
	    opacity: 0 !important;
	}
	.filepond--panel.filepond--item-panel,
	.filepond--item-panel {
	    background: transparent !important;
	}
	.filepond--panel-top,
	.filepond--panel-bottom,
	.filepond--panel-center {
	    display: none !important;
	}

		/* 编辑页：左标题、右编辑框（所有资源通用） */
		.fi-resource-edit-record-page .fi-fo-field-wrp > .grid,
		.fi-resource-create-record-page .fi-fo-field-wrp > .grid {
		    display: grid !important;
		    grid-template-columns: 180px 1fr !important;
		    gap: 12px !important;
		    align-items: start !important;
		}
		.fi-resource-edit-record-page .fi-fo-field-wrp-label,
		.fi-resource-create-record-page .fi-fo-field-wrp-label {
		    padding-top: 0 !important;
		    margin-top: 8px !important;
		    width: 100% !important;
		    justify-content: flex-end !important;
		}
		.fi-resource-edit-record-page .fi-fo-field-wrp > .grid > div:first-child,
		.fi-resource-create-record-page .fi-fo-field-wrp > .grid > div:first-child {
		    justify-content: flex-end !important;
		}
			.fi-resource-edit-record-page .fi-fo-field-wrp-label span:first-child,
			.fi-resource-create-record-page .fi-fo-field-wrp-label span:first-child {
			    text-align: right !important;
			}

		/* 访问日志：收窄行高 */
		.fi-resource-access-logs .fi-ta-cell {
		    padding-block: 0.125rem !important;
		}
		.fi-resource-access-logs .fi-ta-col-text {
		    line-height: 1.25rem !important;
		}

	</style>

 {{-- 订单页：把 headerActions 移到 toolbar row，并隐藏原来的 header-actions 行 --}}
 <style>
     .fi-resource-orders .fi-header-actions {
         display: none !important;
     }

     /* 订单筛选区：左标题，右筛选框，水平排列 */
     .fi-resource-orders .fi-ta-filter .fi-fo-field-wrp {
         display: flex !important;
         flex-direction: row !important;
         align-items: center !important;
         gap: 0.5rem !important;
     }
     .fi-resource-orders .fi-ta-filter .fi-fo-field-wrp-label {
         margin-bottom: 0 !important;
         white-space: nowrap !important;
         min-width: fit-content !important;
     }
 </style>

 {{-- 防止浏览器自动填充登录表单 --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var loginForm = document.getElementById('form');
        if (!loginForm) return;

        // 表单级别 autocomplete
        loginForm.setAttribute('autocomplete', 'off');

        // 对账号和密码输入框使用 readonly 技巧（Chrome 最吃这套）
        loginForm.querySelectorAll('input').forEach(function (input) {
            // 立即设置 readonly，Chrome 读到 readonly 就不会填充
            if (!input.value) {
                input.setAttribute('readonly', 'readonly');
                // 点击/聚焦时移除 readonly，允许正常输入
                var removeReadonly = function () {
                    input.removeAttribute('readonly');
                };
                input.addEventListener('focus', removeReadonly, { once: true });
                input.addEventListener('click', removeReadonly, { once: true });
            }
        });
    });
</script>
