(window.webpackJsonp=window.webpackJsonp||[]).push([["loadAvatar"],{cgLp:function(n,a){var e=$("#avatar-holder");e.on("click",(function(){$("[id*=imageFile]").click().on("change",(function(n){!function(n){if(n.files&&n.files[0]){var a=new FileReader;a.onload=function(n){e.attr("src",n.target.result).width(120).height(120)},a.readAsDataURL(n.files[0])}}(n.currentTarget)}))}))}},[["cgLp","runtime"]]]);