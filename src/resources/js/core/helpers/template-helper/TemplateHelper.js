export default class TemplateHelper {
    /**
     * Prevent dropdown menu from closing inside click
     * */
    static preventDropdownMenuCloseOnInsideClick() {
        $(document).on('click.bs.dropdown.data-api', '.dropdown.keep-inside-clicks-open', function (e) {
            e.stopPropagation();
        });
    }

    /**
     * Move scrollbar horizontally with mouse move
     */
    static moveHorizontalScrollbarWithMouse() {
        $(document).ready(function () {
            const slider = document.querySelector('.custom-scrollbar-with-mouse-move');
            if (slider === null) {
                return
            }
            let isDown = false;
            let startX;
            let scrollLeft;
            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });
            slider.addEventListener('mouseleave', () => {
                isDown = false;
            });
            slider.addEventListener('mouseup', () => {
                isDown = false;
            });
            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 3; //scroll-fast
                slider.scrollLeft = scrollLeft - walk;
            });
        })
    }
}
