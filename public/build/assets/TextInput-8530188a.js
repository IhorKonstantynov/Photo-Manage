import{r as a,f as l,o as n,k as d}from"./app-ff6ec338.js";const i=["value"],f={__name:"TextInput",props:{modelValue:{type:String,required:!0}},emits:["update:modelValue"],setup(u,{expose:t}){const e=a(null);return l(()=>{e.value.hasAttribute("autofocus")&&e.value.focus()}),t({focus:()=>e.value.focus()}),(r,o)=>(n(),d("input",{class:"w-full border-gray-400 border-solid focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm",value:u.modelValue,onInput:o[0]||(o[0]=s=>r.$emit("update:modelValue",s.target.value)),ref_key:"input",ref:e},null,40,i))}};export{f as _};
