'use strict';
document.addEventListener('DOMContentLoaded',()=>{
  const sidebar=document.getElementById('adminSidebar');
  const backdrop=document.getElementById('sidebarBackdrop');
  const toggle=document.getElementById('sidebarToggle');
  const close=()=>{sidebar?.classList.remove('open');backdrop?.classList.remove('show')};
  toggle?.addEventListener('click',()=>{sidebar?.classList.toggle('open');backdrop?.classList.toggle('show')});
  backdrop?.addEventListener('click',close);
  document.querySelectorAll('form[data-confirm]').forEach(form=>form.addEventListener('submit',event=>{if(!window.confirm(form.dataset.confirm||'Are you sure?'))event.preventDefault()}));
});
