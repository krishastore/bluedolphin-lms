import Swiper from 'swiper/bundle';

const swiper = new Swiper(".bdlms-similar-course-slider", {
    loop: true,
    slidesPerView: 1,
    spaceBetween: 10,
    navigation: {
      nextEl: ".bdlms-sc-slider-next",
      prevEl: ".bdlms-sc-slider-prev",
    },
    breakpoints: {
      768: {
        slidesPerView: 2,
        spaceBetween: 32,
      },
      1024: {
        slidesPerView: 3,
        spaceBetween: 32,
      },
    },
});
