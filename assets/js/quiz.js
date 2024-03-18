/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/js/src/quiz.js":
/*!*******************************!*\
  !*** ./assets/js/src/quiz.js ***!
  \*******************************/
/***/ (() => {

eval("/**\r\n * This file contains the functions needed for handle quiz module.\r\n *\r\n * @since 1.0.0\r\n * @output assets/js/questions.js\r\n */\n\nwindow.wp = window.wp || {};\n\n/**\r\n * Manages the quick edit and bulk edit windows for editing posts or pages.\r\n *\r\n * @namespace quizModule\r\n *\r\n * @since 1.0.0\r\n *\r\n * @type {Object}\r\n *\r\n * @property {string} type The type of inline editor.\r\n * @property {string} what The prefix before the post ID.\r\n *\r\n */\n(function ($, wp) {\n  $(\"#add_new_question\").dialog({\n    title: \"From where you want to add a new Question?\",\n    dialogClass: \"wp-dialog bdlms-modal\",\n    autoOpen: false,\n    draggable: false,\n    width: \"auto\",\n    modal: true,\n    resizable: false,\n    closeOnEscape: true,\n    position: {\n      my: \"center\",\n      at: \"center\",\n      of: window\n    },\n    open: function (event, ui) {},\n    create: function () {}\n  });\n  $(\"#questions_bank\").dialog({\n    title: \"Questions Bank\",\n    dialogClass: \"wp-dialog bdlms-modal\",\n    autoOpen: false,\n    draggable: false,\n    width: \"auto\",\n    modal: true,\n    resizable: false,\n    closeOnEscape: true,\n    position: {\n      my: \"center\",\n      at: \"center\",\n      of: window\n    },\n    open: function (event, ui) {},\n    create: function () {}\n  });\n  $(document).on(\"click\", \".add-new-question\", function () {\n    $(\"#add_new_question\").dialog(\"open\");\n  });\n  $(document).on(\"click\", \".open-questions-bank\", function () {\n    $(\"#questions_bank\").dialog(\"open\");\n  });\n})(jQuery, window.wp);\n\n//# sourceURL=webpack://bluedolphin-lms/./assets/js/src/quiz.js?");

/***/ }),

/***/ "./assets/scss/quiz.scss":
/*!*******************************!*\
  !*** ./assets/scss/quiz.scss ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://bluedolphin-lms/./assets/scss/quiz.scss?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	__webpack_modules__["./assets/js/src/quiz.js"](0, {}, __webpack_require__);
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./assets/scss/quiz.scss"](0, __webpack_exports__, __webpack_require__);
/******/ 	
/******/ })()
;