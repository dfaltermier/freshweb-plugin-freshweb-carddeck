/**
 * Provide the fade-in behavior for the card deck.
 *
 */
(function($) {
    $.fn.jqAnimateCardDeck = function(options) {

        'use strict';

        options = $.extend({
            effect: 'none',                // One of: none, fadeAll, fadeEach, swell or pulse
            fadeAll: {
                fadeInDuration: '2000'     // milliseconds
            },
            fadeEach: {
                imageOrder:         'default', // One of: default, sequential or shuffle
                delayBetweenImages: '100',     // milliseconds
                fadeInDuration:     '400'      // milliseconds
            }
        }, options || {});

        return this.each(function() {

            function shuffle(list) {
                for (var j, x, i = list.length; i; j = parseInt(Math.random() * i), x = list[--i], list[
                        i] = list[j], list[j] = x);
                return list;
            }

            function getImageOrder(order) {
                var imageOrder = [];
                var defaultImageOrder = [7, 17, 4, 10, 18, 11, 19, 14, 6, 8, 21, 12, 3, 13, 1, 15, 2, 9, 20, 5, 16];
                var sequentialImageOrder = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];

                switch (order) {
                    case 'default':
                        imageOrder = defaultImageOrder;
                        break;

                    case 'sequential':
                        imageOrder = sequentialImageOrder;
                        break;

                    case 'shuffle':
                        imageOrder = shuffle(sequentialImageOrder);
                        break;

                    default:
                        imageOrder = defaultImageOrder;
                        break;
                }

                return imageOrder;
            }

            function effectNone($images) {
                $images.removeClass('fw-carddeck-image-hidden');
            }

            function effectFadeAll($images, options) {
                var fadeAllOptions = options.fadeAll;

                $images.removeClass('fw-carddeck-image-hidden')
                    .animate({
                        easing: 'swing',
                        opacity: '1'
                    },
                    {
                        duration: parseInt(fadeAllOptions.fadeInDuration)
                    }
                );
            }

            function effectFadeEach($images, options) {

                function animateImage($image, fadeEachOptions) {
                    $image.removeClass('fw-carddeck-image-hidden')
                        .animate(
                            {opacity: '1'},
                            {duration: parseInt(fadeEachOptions.fadeInDuration)}
                        );
                }

                var index = 0;
                var fadeEachOptions = options.fadeEach;
                var imageOrder = getImageOrder(fadeEachOptions.imageOrder);

                // Animate first image immediately
                var $image = $images.eq(imageOrder[index] - 1);
                index++;
                animateImage($image, fadeEachOptions);

                // Animate the remaining images with delay
                var intervalHandle = setInterval(function() {
                    if (index >= imageOrder.length) {
                        clearInterval(intervalHandle);
                        return;
                    }

                    var $image = $images.eq(imageOrder[index] - 1);
                    animateImage($image, fadeEachOptions);
                    index++;
                }, parseInt(fadeEachOptions.delayBetweenImages) );

            }

            // Fetch our images.
            var $images = $('.fw-js-carddeck-image', this);

            switch (options.effect) {
                case 'none':
                    effectNone($images);
                    break;
                case 'fadeAll':
                    effectFadeAll($images, options);
                    break;
                case 'fadeEach':
                    effectFadeEach($images, options);
                    break;
                default:
                    effectNone($images);
                    break;
            }

        });
    };
})(jQuery);