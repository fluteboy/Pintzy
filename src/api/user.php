<?php
session_start();
// var_dump(isset($_SESSION['signupSuccesful']));

if(isset($_SESSION['signupSuccessful']) && $_SESSION['signupSuccessful'] === false) {
    header('location:/projects/pintzy/src/api/signup-form.php');
    exit;
}


?>


<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="shortcut icon" type="image/png" href="/icon.png" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
      integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
      crossorigin="" />
    <script src="https://kit.fontawesome.com/cf32b5773d.js" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
      integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
      crossorigin=""></script>
    <link rel="stylesheet" href="../../dist/output.css" />
    <script defer type="module" src="../js/app.js"></script>
    <script defer type="module" src="../js/ui.js"></script>
    <title>Pintzy - Pin aware your people.</title>
  </head>

  <body class="flex">
    <!-- sidebar -->
    <aside
      class="absolute sidebar  transition-all  backdrop-blur-sm border-2 border-zinc-500 bg-zinc-500 tablet:relative duration-500 z-30 w-14  ">
      <div class="sidebar-content-wrapper transition-all duration-[400ms] sidebar-content-hide bg-zinc-600 h-screen  ">

        <!-- nav -->
        <nav class="border-b border-zinc-500 py-4 px-4 drop-shadow-sm bg-zinc-500">
          <ul class="flex justify-around">
            <li
              class="font-semibold border-r border-r-1 border-r-gray-300 w-4/5 text-center text-color-light text-base">
              <a href="./pins.html">Pins</a>
            </li>
            <li class="font-semibold w-4/5 text-center nav-active text-base">
              Profile
            </li>
          </ul>
        </nav>

        <!-- user info -->
        <div class="signed-user-profile_container flex w-full my-4 py-4 px-5 items-center justify-between">
          <!-- placeholder -->
        </div>

        <!-- pins -->
        <ul class="user-pin-container px-4 py-2 mt-6 flex justify-center items-center flex-col">
          <li
            class="user-pin flex my-1 android-md:w-[22rem] tablet:w-72 rounded-md border border-zinc-200 w-full bg-zinc-100 overflow-hidden">
            <!-- flag -->
            <span class="pin-card_flag inline-block w-3 bg-yellow-200"></span>
            <div class="user-profile-wrapper w-full tablet:w-[98%] pl-3 pr-2 py-2 flex flex-col justify-center">
              <div class="flex justify-between">
                <!-- date -->
                <span class="pin-date w-32 text-[0.6rem] text-gray-400 font-semibold">
                  <img src="../assets/calendar.svg" class="inline-block" />
                  19th Jul, 2023
                </span>
                <!-- time -->
                <span class="pin-time w-32 text-[0.6rem] text-right text-gray-400 font-semibold mr-2">
                  <img src="../assets/time.svg" class="inline-block" />
                  19:15 hrs
                </span>
                <!-- Type -->
                <div
                  class="user-profile-user__pin-count border border-slate-200 bg-white rounded-sm px-1 py-1 text-center flex-grow-0">
                  ⚠️
                </div>
              </div>
              <!-- content -->
              <p class="user-profile-text py-2 mt-4 px-2 border border-slate-300 bg-white text-zinc-600 text-sm">
                Events coming up on July, 19th!
              </p>
            </div>
          </li>
        </ul>


      </div>
      <!-- actions -->
      <div class="user-profile-footer   w-full flex justify-between absolute bottom-16 left-0 px-4 ">
        <button class="btn-user-input w-60 h-10 rounded font-semibold text-m relative android-md:rounded-2xl bg-green-500 text-zinc-50 android-md:bg-transparent border-4
           border-green-500 android-md:border-4 
           laptop:text-gray-700 laptop:bg-transparent
           laptop:hover:bg-green-500 laptop:hover:text-zinc-100 transition-colors active:text-zinc-100 " type="submit"
          name="user-logout">
          Logout
        </button>

        <i
          class="btn-sidebar fa-flip-horizontal fa-solid fa-chevron-left btn-sidebar  rounded-sm  ring-4 ring-zinc-300 text-zinc-100 p-2 ml-4 hover:ring-zinc-500 hover:text-zinc-700 cursor-pointer transition-transform"></i>
      </div>
    </aside>
    <section class="map-content w-full">
      <!-- main content -->

      <div class="ml-4 bg-slate-500">
        <span
          class="font-semibold desktop:ml-6 fixed top-2 right-4 z-20 bg-zinc-400/30 backdrop-blur-sm p-3 rounded-sm">Pintzy</span>
      </div>

      <!-- pop up for input-->
      <section
        class="hidden user-input-bg flex flex-col justify-center left-0 items-center h-screen bg-gradient-to-r from-zinc-700/50 to-zinc-800/60 absolute w-full z-30"
        aria-modal="true">
        <span class="btn-close__user-input absolute bottom-16 tablet-md:top-6 tablet-md:right-8 cursor-pointer"
          aria-label="Close">
          <i
            class="fa-sharp fa-regular fa-circle-xmark fa-2xl text-slate-100 laptop:text-zinc-400 hover:text-zinc-300"></i>
        </span>

        <form action="#"
          class="user-input-form p-4 rounded-sm pb-8 relative border border-gray-300 bg-zinc-300 w-4/5 mt-4 flex justify-center flex-col items-center android-md/2:w-80 tablet-md:w-[26rem] tablet-md:rounded tablet-md:px-6 laptop:h-96"
          id="form-user-input">
          <div class="flex flex-col my-4 w-full">
            <label class="text-gray-600 text-xs mb-1">Pin type</label>
            <select name="eventType" id="eventType" class="p-1 cursor-pointer border border-zinc-300">
              <option value="none">---</option>
              <option value="emergency">Emergency 🚨</option>
              <option value="alert">Alert &#9888;</option>
              <option value="event">Event &#9733;</option>
              <option value="review">Review 🤔</option>
              <option value="touristAttraction">Tourist Attraction 🌐</option>
              <option value="reacreational">Recreational 😎</option>
            </select>
          </div>
          <div class="flex flex-col w-full">
            <label class="text-gray-600 text-xs mb-1">Message</label>

            <textarea class="rounded-sm border border-zinc-300 p-2 resize-none" name="message" id="message" cols="30"
              rows="4"></textarea>
          </div>
          <button
            class="btn-user-input w-full mt-10 mb-3 h-10 rounded font-semibold text-m text-gray-700 android-md/2:w-52 android-md:rounded-2xl android-md:bg-transparent border-4 border-green-500 android-md:border-4 laptop:hover:bg-green-500 laptop:hover:text-zinc-100 transition-colors active:text-zinc-100 disabled:border-zinc-400 disabled:text-zinc-400 disabled:hover:bg-transparent disabled:hover:text-zinc-400"
            type="submit" name="user-submit" disabled>
            Pin
          </button>
        </form>
        ;
      </section>
      <!-- map -->
      <div id="map" class="h-screen z-10"></div>
    </section>
  </body>

</html>
