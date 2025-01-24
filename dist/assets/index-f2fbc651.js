import{P as Ve,av as qe,w as le,az as Q,bo as He,aB as L,aA as R,R as Ne,aE as Fe,bc as re,au as fe,aF as Ie,aG as je,aH as Be,aI as Le,S as K,b as U,s as W,a2 as w,a3 as P,a4 as E,a5 as S,t as p,r as y,F as ve,e as M,p as Oe,q as ke,h as V,a7 as H,v as $,a as v,a1 as ee,k as N,am as k,aK as F,C as h,M as q,D as I,aM as De,A as te,aN as we,_ as ue,aq as ae,J as Ke,K as Ue,N as We,L as Ge,aO as Je}from"./settings-store-9c8686e2.js";import{t as Xe,o as Ye,i as ce,j as Z,l as de,n as Qe,k as _e,m as Ze,A as xe,q as Pe}from"./skeleton-edf9cf6d.js";import{o as $e}from"./separator-f399de31.js";import{d as et,u as tt}from"./action-cf7d3ef6.js";function nt(t){const e=t.slice();return e.sort(ot),it(e)}function it(t){if(t.length<=1)return t.slice();const e=[];for(let i=0;i<t.length;i++){const s=t[i];for(;e.length>=2;){const f=e[e.length-1],r=e[e.length-2];if((f.x-r.x)*(s.y-r.y)>=(f.y-r.y)*(s.x-r.x))e.pop();else break}e.push(s)}e.pop();const n=[];for(let i=t.length-1;i>=0;i--){const s=t[i];for(;n.length>=2;){const f=n[n.length-1],r=n[n.length-2];if((f.x-r.x)*(s.y-r.y)>=(f.y-r.y)*(s.x-r.x))n.pop();else break}n.push(s)}return n.pop(),e.length==1&&n.length==1&&e[0].x==n[0].x&&e[0].y==n[0].y?e:e.concat(n)}function ot(t,e){return t.x<e.x?-1:t.x>e.x?1:t.y<e.y?-1:t.y>e.y?1:0}function st(t){const e=t.getBoundingClientRect();return[{x:e.left,y:e.top},{x:e.right,y:e.top},{x:e.right,y:e.bottom},{x:e.left,y:e.bottom}]}function lt(t){const e=t.flatMap(n=>st(n));return nt(e)}function rt(t,e){let n=!1;for(let i=0,s=e.length-1;i<e.length;s=i++){const f=e[i].x,r=e[i].y,a=e[s].x,c=e[s].y;r>t.y!=c>t.y&&t.x<(a-f)*(t.y-r)/(c-r)+f&&(n=!n)}return n}const ft={positioning:{placement:"bottom"},arrowSize:8,defaultOpen:!1,closeOnPointerDown:!0,openDelay:1e3,closeDelay:0,forceVisible:!1,portal:"body",closeOnEscape:!0},{name:x}=Qe("tooltip");function ut(t){const e={...ft,...t},n=Xe($e(e,"open")),{positioning:i,arrowSize:s,closeOnPointerDown:f,openDelay:r,closeDelay:a,forceVisible:c,portal:o,closeOnEscape:l}=n,u=e.open??le(e.defaultOpen),g=Ye(u,e==null?void 0:e.onOpenChange),T=le(null),O={content:ce(),trigger:ce()};let z=!1;Ve(()=>{qe&&T.set(document.querySelector(`[aria-describedby="${O.content}"]`))});let _=null,d=null;function A(){d&&(window.clearTimeout(d),d=null),_||(_=window.setTimeout(()=>{g.set(!0),_=null},L(r)))}function j(b){_&&(window.clearTimeout(_),_=null),!(b&&J)&&(d||(d=window.setTimeout(()=>{g.set(!1),b&&(z=!1),d=null},L(a))))}const m=Z(x("trigger"),{returned:()=>({"aria-describedby":O.content}),action:b=>({destroy:Q(R(b,"pointerdown",()=>{L(f)&&(g.set(!1),z=!0,_&&(window.clearTimeout(_),_=null))}),R(b,"pointerenter",C=>{re(C)||A()}),R(b,"pointerleave",C=>{re(C)||_&&(window.clearTimeout(_),_=null)}),R(b,"focus",()=>{z||A()}),R(b,"blur",()=>j(!0)),R(b,"keydown",C=>{L(l)&&C.key===Fe.ESCAPE&&(_&&(window.clearTimeout(_),_=null),g.set(!1))}))})}),G=et({open:g,activeTrigger:T,forceVisible:c}),ze=Z(x("content"),{stores:[G,o],returned:([b,D])=>({role:"tooltip",hidden:b?void 0:!0,tabindex:-1,style:_e({display:b?void 0:"none"}),id:O.content,"data-portal":D?"":void 0}),action:b=>{let D=fe,C=fe;const B=de([G,T,i,o],([Re,ie,Me,oe])=>{if(!Re||!ie){C(),D();return}Ne().then(()=>{if(D=tt(ie,b,Me).destroy,!oe){C();return}const se=Ze(b,oe);if(se){const Y=xe(b,se);Y&&Y.destroy&&(C=Y.destroy)}})}),X=Q(R(b,"pointerenter",A),R(b,"pointerdown",A));return{destroy(){X(),C(),D(),B()}}}}),Ae=Z(x("arrow"),{stores:s,returned:b=>({"data-arrow":!0,style:_e({position:"absolute",width:`var(--arrow-size, ${b}px)`,height:`var(--arrow-size, ${b}px)`})})});let J=!1;return de([G,T],([b,D])=>{if(!(!b||!D))return Q(He(document,"mousemove",C=>{const B=document.getElementById(O.content);if(!B)return;const X=lt([D,B]);J=rt({x:C.clientX,y:C.clientY},X),J||document.activeElement===D&&!z?A():j()}))}),{elements:{trigger:m,content:ze,arrow:Ae},states:{open:g},options:n}}const Ee="Tooltip",ne={set:at,get:Se,setArrow:ct};function at(t){const e=ut({positioning:{placement:"top"},openDelay:700,...Ie(t)});return je(Ee,e),{...e,updateOption:Be(e.options)}}function Se(t=0){const e=Le(Ee),{options:{positioning:n}}=e;return n.update(i=>({...i,gutter:t})),e}function ct(t=8){const e=Se();return e.options.arrowSize.set(t),e}function dt(t){let e;const n=t[11].default,i=w(n,t,t[10],null);return{c(){i&&i.c()},m(s,f){i&&i.m(s,f),e=!0},p(s,[f]){i&&i.p&&(!e||f&1024)&&P(i,n,s,s[10],e?S(n,s[10],f,null):E(s[10]),null)},i(s){e||(p(i,s),e=!0)},o(s){y(i,s),e=!1},d(s){i&&i.d(s)}}}function _t(t,e,n){let{$$slots:i={},$$scope:s}=e,{positioning:f=void 0}=e,{arrowSize:r=void 0}=e,{closeOnEscape:a=void 0}=e,{portal:c=void 0}=e,{closeOnPointerDown:o=void 0}=e,{openDelay:l=void 0}=e,{closeDelay:u=void 0}=e,{open:g=void 0}=e,{onOpenChange:T=void 0}=e,{forceVisible:O=!0}=e;const{states:{open:z},updateOption:_}=ne.set({positioning:f,arrowSize:r,closeOnEscape:a,portal:c,closeOnPointerDown:o,openDelay:l,closeDelay:u,forceVisible:O,defaultOpen:g,onOpenChange:({next:d})=>(g!==d&&(T==null||T(d),n(0,g=d)),d)});return t.$$set=d=>{"positioning"in d&&n(1,f=d.positioning),"arrowSize"in d&&n(2,r=d.arrowSize),"closeOnEscape"in d&&n(3,a=d.closeOnEscape),"portal"in d&&n(4,c=d.portal),"closeOnPointerDown"in d&&n(5,o=d.closeOnPointerDown),"openDelay"in d&&n(6,l=d.openDelay),"closeDelay"in d&&n(7,u=d.closeDelay),"open"in d&&n(0,g=d.open),"onOpenChange"in d&&n(8,T=d.onOpenChange),"forceVisible"in d&&n(9,O=d.forceVisible),"$$scope"in d&&n(10,s=d.$$scope)},t.$$.update=()=>{t.$$.dirty&1&&g!==void 0&&z.set(g),t.$$.dirty&2&&_("positioning",f),t.$$.dirty&4&&_("arrowSize",r),t.$$.dirty&8&&_("closeOnEscape",a),t.$$.dirty&16&&_("portal",c),t.$$.dirty&32&&_("closeOnPointerDown",o),t.$$.dirty&64&&_("openDelay",l),t.$$.dirty&128&&_("closeDelay",u),t.$$.dirty&512&&_("forceVisible",O)},[g,f,r,a,c,o,l,u,T,O,s,i]}class mt extends K{constructor(e){super(),U(this,e,_t,dt,W,{positioning:1,arrowSize:2,closeOnEscape:3,portal:4,closeOnPointerDown:5,openDelay:6,closeDelay:7,open:0,onOpenChange:8,forceVisible:9})}}const gt=t=>({builder:t&256}),me=t=>({builder:t[16]}),bt=t=>({builder:t&256}),ge=t=>({builder:t[16]}),ht=t=>({builder:t&256}),be=t=>({builder:t[16]}),pt=t=>({builder:t&256}),he=t=>({builder:t[16]}),yt=t=>({builder:t&256}),pe=t=>({builder:t[16]}),Ct=t=>({builder:t&256}),ye=t=>({builder:t[16]});function Tt(t){const e=t.slice(),n=e[8];return e[16]=n,e}function vt(t){const e=t.slice(),n=e[8];return e[16]=n,e}function Ot(t){const e=t.slice(),n=e[8];return e[16]=n,e}function kt(t){const e=t.slice(),n=e[8];return e[16]=n,e}function Dt(t){const e=t.slice(),n=e[8];return e[16]=n,e}function wt(t){const e=t.slice(),n=e[8];return e[16]=n,e}function Pt(t){let e,n,i,s;const f=t[15].default,r=w(f,t,t[14],me);let a=[t[16],t[12]],c={};for(let o=0;o<a.length;o+=1)c=v(c,a[o]);return{c(){e=N("div"),r&&r.c(),k(e,c)},m(o,l){M(o,e,l),r&&r.m(e,null),n=!0,i||(s=[F(t[16].action(e)),h(e,"m-pointerdown",t[11]),h(e,"m-pointerenter",t[11])],i=!0)},p(o,l){r&&r.p&&(!n||l&16640)&&P(r,f,o,o[14],n?S(f,o[14],l,gt):E(o[14]),me),k(e,c=q(a,[l&256&&o[16],l&4096&&o[12]]))},i(o){n||(p(r,o),n=!0)},o(o){y(r,o),n=!1},d(o){o&&V(e),r&&r.d(o),i=!1,I(s)}}}function Et(t){let e,n,i,s,f;const r=t[15].default,a=w(r,t,t[14],ge);let c=[t[16],t[12]],o={};for(let l=0;l<c.length;l+=1)o=v(o,c[l]);return{c(){e=N("div"),a&&a.c(),k(e,o)},m(l,u){M(l,e,u),a&&a.m(e,null),i=!0,s||(f=[F(t[16].action(e)),h(e,"m-pointerdown",t[11]),h(e,"m-pointerenter",t[11])],s=!0)},p(l,u){t=l,a&&a.p&&(!i||u&16640)&&P(a,r,t,t[14],i?S(r,t[14],u,bt):E(t[14]),ge),k(e,o=q(c,[u&256&&t[16],u&4096&&t[12]]))},i(l){i||(p(a,l),n&&n.end(1),i=!0)},o(l){y(a,l),l&&(n=De(e,t[4],t[5])),i=!1},d(l){l&&V(e),a&&a.d(l),l&&n&&n.end(),s=!1,I(f)}}}function St(t){let e,n,i,s,f;const r=t[15].default,a=w(r,t,t[14],be);let c=[t[16],t[12]],o={};for(let l=0;l<c.length;l+=1)o=v(o,c[l]);return{c(){e=N("div"),a&&a.c(),k(e,o)},m(l,u){M(l,e,u),a&&a.m(e,null),i=!0,s||(f=[F(t[16].action(e)),h(e,"m-pointerdown",t[11]),h(e,"m-pointerenter",t[11])],s=!0)},p(l,u){t=l,a&&a.p&&(!i||u&16640)&&P(a,r,t,t[14],i?S(r,t[14],u,ht):E(t[14]),be),k(e,o=q(c,[u&256&&t[16],u&4096&&t[12]]))},i(l){i||(p(a,l),l&&(n||te(()=>{n=we(e,t[2],t[3]),n.start()})),i=!0)},o(l){y(a,l),i=!1},d(l){l&&V(e),a&&a.d(l),s=!1,I(f)}}}function zt(t){let e,n,i,s,f,r;const a=t[15].default,c=w(a,t,t[14],he);let o=[t[16],t[12]],l={};for(let u=0;u<o.length;u+=1)l=v(l,o[u]);return{c(){e=N("div"),c&&c.c(),k(e,l)},m(u,g){M(u,e,g),c&&c.m(e,null),s=!0,f||(r=[F(t[16].action(e)),h(e,"m-pointerdown",t[11]),h(e,"m-pointerenter",t[11])],f=!0)},p(u,g){t=u,c&&c.p&&(!s||g&16640)&&P(c,a,t,t[14],s?S(a,t[14],g,pt):E(t[14]),he),k(e,l=q(o,[g&256&&t[16],g&4096&&t[12]]))},i(u){s||(p(c,u),u&&te(()=>{s&&(i&&i.end(1),n=we(e,t[2],t[3]),n.start())}),s=!0)},o(u){y(c,u),n&&n.invalidate(),u&&(i=De(e,t[4],t[5])),s=!1},d(u){u&&V(e),c&&c.d(u),u&&i&&i.end(),f=!1,I(r)}}}function At(t){let e,n,i,s,f;const r=t[15].default,a=w(r,t,t[14],pe);let c=[t[16],t[12]],o={};for(let l=0;l<c.length;l+=1)o=v(o,c[l]);return{c(){e=N("div"),a&&a.c(),k(e,o)},m(l,u){M(l,e,u),a&&a.m(e,null),i=!0,s||(f=[F(t[16].action(e)),h(e,"m-pointerdown",t[11]),h(e,"m-pointerenter",t[11])],s=!0)},p(l,u){t=l,a&&a.p&&(!i||u&16640)&&P(a,r,t,t[14],i?S(r,t[14],u,yt):E(t[14]),pe),k(e,o=q(c,[u&256&&t[16],u&4096&&t[12]]))},i(l){i||(p(a,l),l&&te(()=>{i&&(n||(n=ue(e,t[0],t[1],!0)),n.run(1))}),i=!0)},o(l){y(a,l),l&&(n||(n=ue(e,t[0],t[1],!1)),n.run(0)),i=!1},d(l){l&&V(e),a&&a.d(l),l&&n&&n.end(),s=!1,I(f)}}}function Rt(t){let e;const n=t[15].default,i=w(n,t,t[14],ye);return{c(){i&&i.c()},m(s,f){i&&i.m(s,f),e=!0},p(s,f){i&&i.p&&(!e||f&16640)&&P(i,n,s,s[14],e?S(n,s[14],f,Ct):E(s[14]),ye)},i(s){e||(p(i,s),e=!0)},o(s){y(i,s),e=!1},d(s){i&&i.d(s)}}}function Mt(t){let e,n,i,s;const f=[Rt,At,zt,St,Et,Pt],r=[];function a(o,l){return o[6]&&o[7]?0:o[0]&&o[7]?1:o[2]&&o[4]&&o[7]?2:o[2]&&o[7]?3:o[4]&&o[7]?4:o[7]?5:-1}function c(o,l){if(l===0)return wt(o);if(l===1)return Dt(o);if(l===2)return kt(o);if(l===3)return Ot(o);if(l===4)return vt(o);if(l===5)return Tt(o)}return~(e=a(t))&&(n=r[e]=f[e](c(t,e))),{c(){n&&n.c(),i=ve()},m(o,l){~e&&r[e].m(o,l),M(o,i,l),s=!0},p(o,[l]){let u=e;e=a(o),e===u?~e&&r[e].p(c(o,e),l):(n&&(Oe(),y(r[u],1,1,()=>{r[u]=null}),ke()),~e?(n=r[e],n?n.p(c(o,e),l):(n=r[e]=f[e](c(o,e)),n.c()),p(n,1),n.m(i.parentNode,i)):n=null)},i(o){s||(p(n),s=!0)},o(o){y(n),s=!1},d(o){o&&V(i),~e&&r[e].d(o)}}}function Vt(t,e,n){const i=["transition","transitionConfig","inTransition","inTransitionConfig","outTransition","outTransitionConfig","asChild","sideOffset"];let s=H(e,i),f,r,{$$slots:a={},$$scope:c}=e,{transition:o=void 0}=e,{transitionConfig:l=void 0}=e,{inTransition:u=void 0}=e,{inTransitionConfig:g=void 0}=e,{outTransition:T=void 0}=e,{outTransitionConfig:O=void 0}=e,{asChild:z=!1}=e,{sideOffset:_=4}=e;const{elements:{content:d},states:{open:A}}=ne.get(_);$(t,d,m=>n(8,r=m)),$(t,A,m=>n(7,f=m));const j=Pe();return t.$$set=m=>{e=v(v({},e),ee(m)),n(12,s=H(e,i)),"transition"in m&&n(0,o=m.transition),"transitionConfig"in m&&n(1,l=m.transitionConfig),"inTransition"in m&&n(2,u=m.inTransition),"inTransitionConfig"in m&&n(3,g=m.inTransitionConfig),"outTransition"in m&&n(4,T=m.outTransition),"outTransitionConfig"in m&&n(5,O=m.outTransitionConfig),"asChild"in m&&n(6,z=m.asChild),"sideOffset"in m&&n(13,_=m.sideOffset),"$$scope"in m&&n(14,c=m.$$scope)},[o,l,u,g,T,O,z,f,r,d,A,j,s,_,c,a]}class qt extends K{constructor(e){super(),U(this,e,Vt,Mt,W,{transition:0,transitionConfig:1,inTransition:2,inTransitionConfig:3,outTransition:4,outTransitionConfig:5,asChild:6,sideOffset:13})}}const Ht=t=>({builder:t&2}),Ce=t=>({builder:t[7]}),Nt=t=>({builder:t&2}),Te=t=>({builder:t[1]});function Ft(t){const e=t.slice(),n=e[1];return e[7]=n,e}function It(t){let e,n,i,s;const f=t[6].default,r=w(f,t,t[5],Ce);let a=[t[7],t[4]],c={};for(let o=0;o<a.length;o+=1)c=v(c,a[o]);return{c(){e=N("button"),r&&r.c(),k(e,c)},m(o,l){M(o,e,l),r&&r.m(e,null),e.autofocus&&e.focus(),n=!0,i||(s=[F(t[7].action(e)),h(e,"m-blur",t[3]),h(e,"m-focus",t[3]),h(e,"m-keydown",t[3]),h(e,"m-pointerdown",t[3]),h(e,"m-pointerenter",t[3]),h(e,"m-pointerleave",t[3])],i=!0)},p(o,l){r&&r.p&&(!n||l&34)&&P(r,f,o,o[5],n?S(f,o[5],l,Ht):E(o[5]),Ce),k(e,c=q(a,[l&2&&o[7],l&16&&o[4]]))},i(o){n||(p(r,o),n=!0)},o(o){y(r,o),n=!1},d(o){o&&V(e),r&&r.d(o),i=!1,I(s)}}}function jt(t){let e;const n=t[6].default,i=w(n,t,t[5],Te);return{c(){i&&i.c()},m(s,f){i&&i.m(s,f),e=!0},p(s,f){i&&i.p&&(!e||f&34)&&P(i,n,s,s[5],e?S(n,s[5],f,Nt):E(s[5]),Te)},i(s){e||(p(i,s),e=!0)},o(s){y(i,s),e=!1},d(s){i&&i.d(s)}}}function Bt(t){let e,n,i,s;const f=[jt,It],r=[];function a(o,l){return o[0]?0:1}function c(o,l){return l===1?Ft(o):o}return e=a(t),n=r[e]=f[e](c(t,e)),{c(){n.c(),i=ve()},m(o,l){r[e].m(o,l),M(o,i,l),s=!0},p(o,[l]){let u=e;e=a(o),e===u?r[e].p(c(o,e),l):(Oe(),y(r[u],1,1,()=>{r[u]=null}),ke(),n=r[e],n?n.p(c(o,e),l):(n=r[e]=f[e](c(o,e)),n.c()),p(n,1),n.m(i.parentNode,i))},i(o){s||(p(n),s=!0)},o(o){y(n),s=!1},d(o){o&&V(i),r[e].d(o)}}}function Lt(t,e,n){const i=["asChild"];let s=H(e,i),f,{$$slots:r={},$$scope:a}=e,{asChild:c=!1}=e;const o=ne.get().elements.trigger;$(t,o,u=>n(1,f=u));const l=Pe();return t.$$set=u=>{e=v(v({},e),ee(u)),n(4,s=H(e,i)),"asChild"in u&&n(0,c=u.asChild),"$$scope"in u&&n(5,a=u.$$scope)},[c,f,o,l,s,a,r]}class Kt extends K{constructor(e){super(),U(this,e,Lt,Bt,W,{asChild:0})}}function Ut(t){let e;const n=t[5].default,i=w(n,t,t[6],null);return{c(){i&&i.c()},m(s,f){i&&i.m(s,f),e=!0},p(s,f){i&&i.p&&(!e||f&64)&&P(i,n,s,s[6],e?S(n,s[6],f,null):E(s[6]),null)},i(s){e||(p(i,s),e=!0)},o(s){y(i,s),e=!1},d(s){i&&i.d(s)}}}function Wt(t){let e,n;const i=[{sideOffset:t[1]},{transition:t[2]},{transitionConfig:t[3]},{class:ae("z-50 overflow-hidden rounded-md bg-primary px-3 py-1.5 text-xs text-primary-foreground",t[0])},t[4]];let s={$$slots:{default:[Ut]},$$scope:{ctx:t}};for(let f=0;f<i.length;f+=1)s=v(s,i[f]);return e=new qt({props:s}),{c(){Ke(e.$$.fragment)},m(f,r){Ue(e,f,r),n=!0},p(f,[r]){const a=r&31?q(i,[r&2&&{sideOffset:f[1]},r&4&&{transition:f[2]},r&8&&{transitionConfig:f[3]},r&1&&{class:ae("z-50 overflow-hidden rounded-md bg-primary px-3 py-1.5 text-xs text-primary-foreground",f[0])},r&16&&We(f[4])]):{};r&64&&(a.$$scope={dirty:r,ctx:f}),e.$set(a)},i(f){n||(p(e.$$.fragment,f),n=!0)},o(f){y(e.$$.fragment,f),n=!1},d(f){Ge(e,f)}}}function Gt(t,e,n){const i=["class","sideOffset","transition","transitionConfig"];let s=H(e,i),{$$slots:f={},$$scope:r}=e,{class:a=void 0}=e,{sideOffset:c=4}=e,{transition:o=Je}=e,{transitionConfig:l={y:8,duration:150}}=e;return t.$$set=u=>{e=v(v({},e),ee(u)),n(4,s=H(e,i)),"class"in u&&n(0,a=u.class),"sideOffset"in u&&n(1,c=u.sideOffset),"transition"in u&&n(2,o=u.transition),"transitionConfig"in u&&n(3,l=u.transitionConfig),"$$scope"in u&&n(6,r=u.$$scope)},[a,c,o,l,s,f,r]}class xt extends K{constructor(e){super(),U(this,e,Gt,Wt,W,{class:0,sideOffset:1,transition:2,transitionConfig:3})}}const $t=mt,en=Kt;export{$t as R,en as T,xt as a};
