import{_ as i}from"./AuthenticatedLayout-528906eb.js";import{T as u,o as e,c,w as r,b as p,u as h,Z as m,d as t,k as s,p as n,D as g,n as x}from"./app-ff6ec338.js";import"./themeOverrides-9d5c4a9b.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./ResponsiveNavLink-9a120b43.js";import"./Copy-ca33d561.js";import"./TextInput-8530188a.js";import"./PrimaryButton-2e8ef213.js";const y=t("h2",{class:"font-semibold text-3xl text-gray-800 leading-tight text-center"},"Refund Request",-1),f=t("h2",{class:"font-semibold text-lg md:text-xl text-gray-600 leading-tight text-center"},"We're sorry things didn't work out, and are happy to refund your purchase.",-1),_=t("h2",{class:"font-semibold text-lg md:text-xl text-gray-600 leading-tight text-center"},"You can process your refund automatically if you have not downloaded any of your headshots.",-1),b={class:"pb-12"},w={class:"max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6"},k={class:"text-center"},R={key:1,class:"bg-gray-400 text-white px-4 py-3 rounded-md border-0 cursor-pointer"},v={key:2,class:"bg-gray-400 text-white px-4 py-3 rounded-md border-0 cursor-pointer"},N={key:0,class:"font-semibold text-lg md:text-xl text-primary leading-tight text-center"},S={key:1,class:"font-semibold text-lg md:text-xl text-red-600 leading-tight text-center"},Y={__name:"Refund",props:{photo:{type:Object},message:{type:String}},setup(o){const d=u({}),l=()=>{d.post(route("refund.process"),{onFinish:()=>{console.log("finished")}})};return(a,$)=>(e(),c(i,null,{header:r(()=>[y,f,_]),default:r(()=>[p(h(m),{title:"| Refund"}),t("div",b,[t("div",w,[t("div",k,[a.$page.props.auth.user.last_payment&&a.$page.props.auth.user.last_payment!="refunded"?(e(),s("button",{key:0,class:"bg-primary text-white px-4 py-3 rounded-md border-0 cursor-pointer",onClick:l},"Request Refund")):a.$page.props.auth.user.last_payment&&a.$page.props.auth.user.last_payment=="refunded"?(e(),s("button",R,"Refunded")):(e(),s("button",v,"Can't Refund"))]),o.message?(e(),s("h3",N,n(o.message),1)):o.photo.downloaded&&JSON.parse(o.photo.downloaded).length>0?(e(),s("h3",S," Sorry, you've already downloaded "+n(JSON.parse(o.photo.downloaded).length)+" images. Your refund request will be reviewed manually. ",1)):g("",!0)])]),x("0 ")]),_:1}))}};export{Y as default};
