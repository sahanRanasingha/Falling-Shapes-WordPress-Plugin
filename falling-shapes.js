jQuery(document).ready(function ($) {
    const shapes = fallingShapesData.images || [];

    function createFallingShape() {
        if (shapes.length === 0) return;

        const size = Math.floor(Math.random() * 25) + 1; // Random size between 1x1 and 25x25 pixels
        const shape = $('<img class="falling-shape">')
            .attr('src', shapes[Math.floor(Math.random() * shapes.length)])
            .css({
                position: 'fixed', // Fixed position to keep shapes within the viewport
                top: '-100px',
                left: Math.random() * ($(window).width() - size - 4) + 2 + 'px', // Random horizontal start position
                width: `${size}px`,
                height: `${size}px`,
                zIndex: 999999, // Very high z-index to ensure visibility
                opacity: Math.random() * 0.5 + 0.5
            });

        $('body').append(shape);

        // Randomly oscillate left and right as the shape falls
        const fallDuration = Math.random() * 3000 + 3000; // Random fall duration between 3000ms and 6000ms
        const amplitude = Math.random() * 100 + 50; // Random horizontal movement amplitude

        shape.animate({
            top: $(window).height() - size + 'px', // Fall to the bottom
            left: `+=${Math.random() > 0.5 ? amplitude : -amplitude}px` // Move left or right
        }, fallDuration, 'linear', function () {
            shape.remove();
        });
    }

    // High density: create shapes at shorter intervals
    setInterval(createFallingShape, 50);
});
