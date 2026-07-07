@push('update-box')
    <script>
        if (typeof window.SimpleLinkedMessageManagerInitialized === 'undefined') {
            window.SimpleLinkedMessageManagerInitialized = true;

            var SimpleLinkedMessageManager = {
            container: null,
            $message: null,
            timer: null,
            apiBaseUrl: '/api/buyer-message',

            init: function() {
                var self = this;

                if (!this.container) {
                    var $allBoxes = $('.update-box');
                    if ($allBoxes.length > 0) {
                        this.container = $allBoxes.first();
                    } else {
                        this.container = $('<div class="update-box"></div>');
                        $('body').append(this.container);
                    }
                    $('.update-box').slice(1).remove();
                }

                this.$message = this.container.find('.update-item').first();
                if (this.$message.length === 0) {
                    this.$message = $('<div class="update-item"></div>');
                    this.container.append(this.$message);
                }

                this.container.find('.update-item').slice(1).remove();

                this.initializePageCounters();
                this.loadBoxBuyers();
                this.start();
            },

            initializePageCounters: function() {
                var self = this;
                var processedBoxNums = {};

                $('.box-count').each(function() {
                    var $elem = $(this);
                    var boxNum = null;

                    var $boxNum = $elem.find('.box-num');
                    if ($boxNum.length > 0) {
                        boxNum = parseInt($boxNum.text());
                    }

                    if (boxNum && !isNaN(boxNum)) {
                        $elem.attr('data-box-count', boxNum);

                        var $buyerCount = null;

                        var $labelSec = $elem.closest('.goods-info-card').find('.goods-label-sec');
                        if ($labelSec.length > 0) {
                            $buyerCount = $labelSec.find('.box-buyer-count');
                        }

                        if (!$buyerCount || $buyerCount.length === 0) {
                            $buyerCount = $('.box-buyer-count[data-box-count="' + boxNum + '"]');
                        }

                        if (!$buyerCount || $buyerCount.length === 0) {
                            var $container = $elem.closest('.goods-card');
                            if ($container.length > 0) {
                                var $subname = $container.find('.goods-subname');
                                if ($subname.length > 0) {
                                    $buyerCount = $('<p class="box-buyer-count" data-box-count="' + boxNum + '">近24小時已有0人訂購</p>');
                                    $subname.after($buyerCount);
                                }
                            }
                        }

                        processedBoxNums[boxNum] = true;
                    }
                });
            },

            loadBoxBuyers: function() {
                var self = this;

                $.ajax({
                    url: this.apiBaseUrl + '/box-buyers',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data) {
                            for (var boxNum in response.data) {
                                var countText = '近24小時已有' + response.data[boxNum] + '人訂購';
                                $('.box-buyer-count[data-box-count="' + boxNum + '"]').text(countText);
                            }
                        }
                    }
                });
            },

            getNextMessage: function(callback) {
                $.ajax({
                    url: this.apiBaseUrl + '/next-message',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data) {
                            callback(response.data);
                        }
                    },
                    error: function() {
                        callback({
                            shouldShow: false,
                            nextInterval: 5000,
                        });
                    }
                });
            },

            showMessage: function() {
                var self = this;

                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }

                this.getNextMessage(function(result) {
                    if (!result.shouldShow) {
                        if (result.nextInterval === 0) {
                            var now = new Date();
                            var currentHour = now.getHours();
                            var nextHour = (currentHour + 1) % 24;
                            var nextHourTime = new Date(now);
                            nextHourTime.setHours(nextHour, 0, 0, 0);
                            if (nextHour < currentHour) {
                                nextHourTime.setDate(nextHourTime.getDate() + 1);
                            }
                            var timeUntilNextHour = nextHourTime.getTime() - now.getTime();
                            self.timer = setTimeout(function() {
                                self.showMessage();
                            }, timeUntilNextHour);
                        } else {
                            self.timer = setTimeout(function() {
                                self.showMessage();
                            }, result.nextInterval);
                        }
                        return;
                    }

                    var messageHtml = result.messageHtml;
                    var boxBuyers = result.boxBuyers;

                    if (boxBuyers) {
                        for (var key in boxBuyers) {
                            var countText = '近24小時已有' + boxBuyers[key] + '人訂購';
                            $('.box-buyer-count[data-box-count="' + key + '"]').text(countText);
                        }
                    }

                    self.$message.html(messageHtml + '<p>剛剛</p>');

                    self.$message.removeClass('slide-in fade-out');
                    self.$message.css({
                        'transform': 'translateX(120%)',
                        'opacity': '0'
                    });

                    requestAnimationFrame(function() {
                        self.$message[0].offsetHeight;

                        requestAnimationFrame(function() {
                            self.$message.css({
                                'transform': '',
                                'opacity': ''
                            });
                            self.$message.addClass('slide-in');

                            setTimeout(function() {
                                self.$message.removeClass('slide-in').addClass('fade-out');

                                setTimeout(function() {
                                    self.timer = setTimeout(function() {
                                        self.showMessage();
                                    }, result.nextInterval);
                                }, 400);
                            }, 3000);
                        });
                    });
                });
            },

            start: function() {
                var self = this;

                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }

                this.timer = setTimeout(function() {
                    self.showMessage();
                }, 3000);
            },

            stop: function() {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }
            }
        };

        $(document).ready(function() {
            SimpleLinkedMessageManager.init();
        });
        }
    </script>
@endpush

<div class="update-box">
    <div class="update-item"></div>
</div>
