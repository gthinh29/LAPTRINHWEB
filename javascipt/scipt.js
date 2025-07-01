document.getElementById("searchInput").addEventListener("keyup", function () {
  const keyword = this.value.toLowerCase();
  const posts = document.querySelectorAll(".post");
  posts.forEach(post => {
    const title = post.querySelector("h2").textContent.toLowerCase();
    post.style.display = title.includes(keyword) ? "" : "none";
  });
});