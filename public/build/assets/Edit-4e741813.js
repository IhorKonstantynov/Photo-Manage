import{_ as r}from"./AuthenticatedLayout-528906eb.js";import c from"./DeleteUserForm-baa7f344.js";import d from"./UpdatePasswordForm-b119272c.js";import l from"./SetPromoCodeForm-d884d25e.js";import p from"./UpdateProfileInformationForm-1a3324b7.js";import{i as n}from"./index-3ffce261.js";import{i as u,o as a,c as _,w as i,b as t,u as f,Z as h,d as s,k as x,D as w}from"./app-ff6ec338.js";import"./themeOverrides-9d5c4a9b.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./ResponsiveNavLink-9a120b43.js";import"./Copy-ca33d561.js";import"./TextInput-8530188a.js";import"./PrimaryButton-2e8ef213.js";import"./InputError-569fabe0.js";import"./InputLabel-a599b044.js";const g=s("h2",{class:"font-semibold text-xl text-white leading-tight"},"Profile",-1),b={class:"pb-12"},y={class:"max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6"},v={class:"p-4 sm:p-8 bg-white shadow border sm:rounded-lg"},$={class:"p-4 sm:p-8 bg-white shadow border sm:rounded-lg"},k={key:0,class:"p-4 sm:p-8 bg-white shadow border sm:rounded-lg"},B={class:"p-4 sm:p-8 bg-white shadow border sm:rounded-lg"},J={__name:"Edit",props:{mustVerifyEmail:{type:Boolean},status:{type:String}},setup(e){const o=e;return u(()=>o.status,()=>{o.status=="promo-updated"&&n.success("Promo code added successfully!",{atoClose:3e3})}),(m,V)=>(a(),_(r,null,{header:i(()=>[g]),default:i(()=>[t(f(h),{title:"Profile"}),s("div",b,[s("div",y,[s("div",v,[t(p,{"must-verify-email":e.mustVerifyEmail,status:e.status,class:"max-w-xl"},null,8,["must-verify-email","status"])]),s("div",$,[t(d,{class:"max-w-xl"})]),m.$page.props.auth.user.account_type==0?(a(),x("div",k,[t(l,{class:"max-w-xl"})])):w("",!0),s("div",B,[t(c,{class:"max-w-xl"})])])])]),_:1}))}};export{J as default};