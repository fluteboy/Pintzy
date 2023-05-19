<?php

// You need to start the session where you need to have a session access.
session_start();

$userName = "Someting_wong";
$userLogged = false;

if(isset($_SESSION["id"])) {

    $userName = $_SESSION["userName"];
    $userLogged = true;

} else {
    $userLogged= false;
    header("location:../../index.php?message=access_denied");
    exit();
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
    <link rel="stylesheet" href="../style/leaflet.css">
    <!-- <script defer type="module" src="../js/app.js"></script>
    <script defer type="module" src="../js/ui.js"></script> -->
    <script type="module" src="../js/init.js"></script>
    <title>Pinzy - Pin aware your people.</title>
  </head>

  <body class="flex relative ">
    <!-- sidebar -->

    <aside
      class="absolute sidebar transition-all bg-zinc-500 duration-500 z-30 w-14 tablet-md:relative tablet:bg-aside tablet:w-[24rem] laptop:w-[30rem]">

      <div class="sidebar-content-wrapper transition-all duration-[400ms] h-screen opacity-0 tablet:opacity-100">

        <!-- nav -->
        <nav class="border-b border-zinc-400 py-4 px-4 drop-shadow-sm bg-zinc-400/60">
          <ul class="flex justify-around">
            <li
              class=" border-r border-r-1 border-r-gray-300 w-4/5 text-center text-zinc-100 opacity-40 text-base hover:font-semibold hover:opacity-100 ">
              <a href="./pins.php">Pins</a>
            </li>
            <li class="font-semibold w-4/5 text-center nav-active text-base">
              Profile
            </li>
          </ul>
        </nav>

        <!-- user info -->
        <div class="signed-user-profile_container flex w-full my-4 py-4 px-5 items-center justify-between">
          <!-- placeholder -->
          <div
            class="user-profile_header-user-image border border-slate-300 w-16 h-16 rounded-full p-2 bg-white flex justify-center items-center">
            <img src="../assets/user-icon-large.svg" alt="user profile" />
          </div>
          <span class="user-profile-header_user-name ml-2 inline-block font-semibold text-zinc-100 text-2xl">
            <?php
           
echo $userLogged? $userName: '';
?>
          </span>

          <div
            class="user-profile__user-pin-count border border-slate-300 bg-zinc-300 rounded-sm px-1 py-1 text-center">
            <span class="user-profile__pin-count__digit font-semibold tablet:text-sm max-w-[2rem]">

            </span>
            <i class="fa-solid fa-location-dot text-slate-500"></i>
          </div>
        </div>

        <!-- pins -->
        <div class="pin-container-wrapper__global bg-zinc-700 h-76vh laptop:h-76vh/20">
          <p class="default-msg text-center text-zinc-400 font-semibold text-lg top-8 relative italic">
            No pins created yet.
          </p>
          <ul
            class="user-pin-container pin-container hidden px-4 pt-8 pb-4 flex items-center flex-col bg-zinc-700 h-65vh overflow-y-scroll">
            <!-- placeholder -->
          </ul>

        </div>
      </div>
      <!-- side footer actions -->

      <div
        class="user-profile-footer w-full flex justify-center absolute bottom-6 left-0 px-4 py-2 tablet:justify-between">

        <a class="btn-user-logout font-semibold px-4 text-lg flex justify-center rounded-sm ring-4 ring-zinc-300 text-zinc-300 items-center transition-all hover:text-zinc-50 hover:font-bold hover:ring-zinc-200 hover:shadow-lg hover:shadow-zinc-800 hidden tablet:flex"
          href="../api/inc/logout.inc.php">Logout</a>


        <i
          class="btn-sidebar fa-solid fa-chevron-left rounded-sm ring-4 ring-zinc-300 text-zinc-300 p-3 hover:text-zinc-100 hover:font-bold hover:ring-zinc-100 cursor-pointer transition-transform hover:shadow-lg hover:shadow-zinc-800"></i>
      </div>
    </aside>
    <section class="map-content w-full">
      <!-- main content -->

      <div class="ml-4 bg-slate-500">
        <span
          class="font-semibold desktop:ml-6 fixed top-2 right-4 z-20 bg-zinc-400/30 backdrop-blur-sm p-3 rounded-sm">Pinzy</span>
      </div>

      <!-- pop up for input-->

      <section
        class="user-input-bg hidden flex flex-col justify-center left-0 items-center h-screen bg-gradient-to-r from-zinc-700/50 to-zinc-800/60 absolute w-full z-30"
        role="dialog">
        <span class="btn-close__user-input absolute bottom-16 tablet-md:top-16 cursor-pointer" role="button"
          aria-label="Close">
          <i
            class="fa-sharp fa-regular fa-circle-xmark fa-2xl text-zinc-400 laptop:text-zinc-500 hover:text-zinc-300"></i>
        </span>

        <form action="#"
          class="user-input-form p-4 rounded-sm pb-8 relative border border-zinc-600/50 bg-zinc-400/60 backdrop-blur-sm w-4/5 mt-4 flex justify-center flex-col items-center android-md/2:w-80 tablet-md:w-[26rem] tablet-md:rounded tablet-md:px-6 laptop:h-96"
          id="form-user-input">
          <div class="flex flex-col my-4 w-full">
            <label class="text-gray-600 text-xs mb-1" for="eventType">Pin type</label>
            <select name="eventType" id="eventType" class="p-1 cursor-pointer border border-zinc-300">
              <option value="none">---</option>
              <option value="emergency" data-icon="🚨" data-color="-red-500">
                Emergency 🚨
              </option>
              <option value="alert" data-icon="&#9888;" data-color="-yellow-500">
                Alert &#9888;
              </option>
              <option value="event" data-icon="&#9733;" data-color="-orange-500">
                Event &#9733;
              </option>
              <option value="review" data-icon="🤔" data-color="-violet-500">
                Review 🤔
              </option>
              <option value="touristAttraction" data-icon="🌐" data-color="-teal-500">
                Tourist Attraction 🌐
              </option>
              <option value="recreational" data-icon="😎" data-color="-yellow-900">
                Recreational 😎
              </option>
            </select>
          </div>

          <div class="flex flex-col w-full">
            <label class="text-gray-600 text-xs mb-1" for="message">Message</label>

            <textarea class="rounded-sm border border-zinc-300 p-2 resize-none" name="message" id="message" cols="30"
              rows="4"></textarea>
          </div>
          <button
            class="btn-user-input w-full mt-10 mb-3 h-10 rounded font-semibold text-m text-zinc-300 android-md/2:w-52 android-md:rounded-2xl ring-4 ring-zinc-300 transition-all hover:text-zinc-50 hover:font-bold hover:ring-zinc-200 active:text-zinc-100 disabled:ring-zinc-400 disabled:!text-zinc-500 disabled:!font-normal disabled:!bg-transparent disabled:hover:shadow-none laptop:hover:bg-zinc-400 laptop:hover:text-zinc-100 laptop:hover:border-zinc-400 hover:shadow-lg hover:shadow-zinc-700"
            type="submit" name="user-submit" disabled>
            Pin
          </button>
        </form>
        ;
      </section>

      <!-- pop up for edit -->
      <section
        class="hidden user-input-bg__edit flex flex-col justify-center left-0 items-center h-screen bg-gradient-to-r from-zinc-700/50 to-zinc-800/60 absolute w-full z-30"
        role="dialog">
        <span class="btn-close__user-input absolute bottom-16 tablet-md:top-16 cursor-pointer" role="button"
          aria-label="Close">
          <i
            class="fa-sharp fa-regular fa-circle-xmark fa-2xl text-zinc-400 laptop:text-zinc-500 hover:text-zinc-300"></i>
        </span>

        <form action="#"
          class="user-input-form__edit p-4 rounded-sm pb-8 relative border border-zinc-600/50 bg-zinc-400/60 backdrop-blur-sm w-4/5 mt-4 flex justify-center flex-col items-center android-md/2:w-80 tablet-md:w-[26rem] tablet-md:rounded tablet-md:px-6 laptop:h-96"
          id="form-user-input__edit">
          <div class="flex flex-col my-4 w-full">
            <label class="text-gray-600 text-xs mb-1" for="eventType__edit">Pin type</label>
            <select name="eventType__edit" id="eventType__edit" class="p-1 cursor-pointer border border-zinc-300">
              <option value="none">---</option>
              <option value="emergency" data-icon="🚨" data-color="-red-500">
                Emergency 🚨
              </option>
              <option value="alert" data-icon="&#9888;" data-color="-yellow-500">
                Alert &#9888;
              </option>
              <option value="event" data-icon="&#9733;" data-color="-orange-500">
                Event &#9733;
              </option>
              <option value="review" data-icon="🤔" data-color="-violet-500">
                Review 🤔
              </option>
              <option value="touristAttraction" data-icon="🌐" data-color="-teal-500">
                Tourist Attraction 🌐
              </option>
              <option value="recreational" data-icon="😎" data-color="-yellow-900">
                Recreational 😎
              </option>
            </select>
          </div>
          <div class="flex flex-col w-full">
            <label class="text-gray-600 text-xs mb-1" for="message__edit">Message</label>

            <textarea class="rounded-sm border border-zinc-300 p-2 resize-none" name="message__edit" id="message__edit"
              cols="30" rows="4"></textarea>
          </div>
          <button
            class="btn-user-input__edit w-full mt-10 mb-3 h-10 rounded font-semibold text-m text-zinc-300 android-md/2:w-52 android-md:rounded-2xl ring-4 ring-zinc-300 transition-all hover:text-zinc-50 hover:font-bold hover:ring-zinc-200 active:text-zinc-100 disabled:ring-zinc-400 disabled:!text-zinc-500 disabled:!font-normal disabled:!bg-transparent disabled:hover:shadow-none laptop:hover:bg-zinc-400 laptop:hover:text-zinc-100 laptop:hover:border-zinc-400 hover:shadow-lg hover:shadow-zinc-700"
            type="submit" name="guest-submit-edit">
            Pin
          </button>
        </form>
        ;
      </section>

      <!-- map -->
      <div class="map-container h-screen z-10 flex justify-center items-center">
        <div class="loader-wrapper flex justify-between items-center w-60 absolute top-80">
          <div class="spinner spin z-20">
            <img src="../assets/spinner.svg" alt="globe" class="w-16" />
          </div>

          <span class="text-4xl text-zinc-600"> Loading...</span>
        </div>
        <div id="map" class="h-screen z-10 w-full"></div>
      </div>
    </section>
  </body>

</html>
