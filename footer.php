
<footer class="custom-footer">
  <style>
    .custom-footer {
      text-align: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 14px;
      padding: 25px 20px;
      margin-top: 50px; /* Adds space above the footer */
      background-color: sandybrown;
      color: #fff;
      position: relative;
      overflow: hidden;
    }

    .custom-footer::before {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      height: 100%;
      width: 100%;
      background: linear-gradient(120deg, transparent, rgba(255,255,255,0.4), transparent);
      animation: arrowFlow 3s linear infinite;
    }

    @keyframes arrowFlow {
      0% { left: -100%; }
      100% { left: 100%; }
    }
  </style>

  &copy; 2025 GLBCL Secure Online Voting System. All rights reserved.
</footer>
