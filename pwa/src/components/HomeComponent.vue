<template>
    <div class="home">
        <h1>NETO BOXING CENTER</h1>
    </div>
  </template>
  
  <script>
  export default {
    name: 'HomeComponent',
    props: {
    }
  }
  </script>
  
  <!-- Add "scoped" attribute to limit CSS to this component only -->
  <style scoped>
  @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap');
  
    h1 {
        font-family: 'Bebas Neue', sans-serif;
        color: rgb(175, 181, 0);
    }
  .home {
    background-image: url(../assets/img/fondo5.jpg);
    background-color: #474747;
    background-blend-mode: soft-light;
    background-size: cover;
    color: rgb(175, 181, 0);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    background-position: center;
    height: 90vh;
  }
  </style>
  