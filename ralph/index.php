<?php
/**
 * Ralph Tuliao - Personal Portfolio Website
 * Modern Red & Black Theme
 * Powered by PHP & MySQL
 */
session_start();

// Direct GET logout handler
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_unset();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        setcookie(session_name(), '', time() - 42000, '/');
        setcookie(session_name(), '', time() - 42000);
    }
    @session_destroy();
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/db.php';

// Auth state for view
$loggedIn  = !empty($_SESSION['user_id']) && !empty($_SESSION['username']);
$authUser  = $loggedIn ? htmlspecialchars((string)$_SESSION['username']) : '';

// Fetch dynamic data
$profile = fetchProfileInfo();
$projects = fetchProjects('all');
$skills = fetchSkills();

// Group skills by category
$skillsByCategory = [];
foreach ($skills as $skill) {
    $skillsByCategory[$skill['category']][] = $skill;
}

// Brand logo image (Ralph Tuliao's portrait)
$brandLogo = file_exists(__DIR__ . '/' . $profile['avatar']) 
    ? $profile['avatar'] 
    : 'assets/images/ralph-tuliao.jpg';

// Official Resume Image resolution (supports assets/resume/ and assets/images/)
$resumeImage = file_exists(__DIR__ . '/assets/resume/ralph-tuliao-resume.png') 
    ? 'assets/resume/ralph-tuliao-resume.png' 
    : (file_exists(__DIR__ . '/assets/images/ralph-tuliao-resume.png') 
        ? 'assets/images/ralph-tuliao-resume.png' 
        : 'assets/resume/ralph-tuliao-resume.png');
$resumeVersion = file_exists(__DIR__ . '/' . $resumeImage) ? filemtime(__DIR__ . '/' . $resumeImage) : time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($profile['full_name']) ?> | Full-Stack Developer & IoT Engineer</title>
  
  <meta name="description" content="Portfolio of Ralph Tuliao - Full-Stack Web Developer and IoT Engineer specializing in dynamic web apps like Tapseafood & Hotel Booking, and smart hardware prototypes like the Arduino Water Dispenser.">
  <meta name="keywords" content="Ralph Tuliao, Portfolio, Web Developer, IoT Engineer, Tapseafood, Hotel Booking, Arduino Water Dispenser, PHP, MySQL, JavaScript">
  <meta name="author" content="Ralph Tuliao">

  <!-- Open Graph -->
  <meta property="og:title" content="<?= htmlspecialchars($profile['full_name']) ?> | Portfolio">
  <meta property="og:description" content="Explore modern web applications and smart IoT hardware solutions by Ralph Tuliao.">
  <meta property="og:image" content="<?= htmlspecialchars($profile['avatar']) ?>">
  <meta property="og:type" content="website">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Ambient Glow Effects -->
  <div class="ambient-glow glow-1"></div>
  <div class="ambient-glow glow-2"></div>

  <!-- Cursor Glow Follower (desktop only) -->
  <div class="cursor-glow" id="cursorGlow" aria-hidden="true"></div>

  <!-- Back to Top Button -->
  <button class="back-to-top" id="backToTopBtn" aria-label="Back to top" title="Back to top">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
  </button>

  <!-- ==========================================================
       Navigation Bar
       ========================================================== -->
  <header class="site-header" id="siteHeader">
    <div class="container nav-wrapper">
      <a href="#hero" class="brand-logo" aria-label="Ralph Tuliao Home">
        <div class="brand-monogram">
          <img src="<?= htmlspecialchars($brandLogo) ?>" alt="<?= htmlspecialchars($profile['full_name']) ?>" class="brand-logo-img">
        </div>
        <div class="brand-text">
          <span class="brand-name">Ralph Tuliao</span>
          <span class="brand-role">Developer & IoT</span>
        </div>
      </a>

      <nav>
        <ul class="nav-links">
          <li><a href="#hero" class="nav-link active">Home</a></li>
          <li><a href="#projects" class="nav-link">Projects</a></li>
          <li><a href="#skills" class="nav-link">Skills</a></li>
          <li><a href="#about" class="nav-link">About</a></li>
          <li><a href="#interview" class="nav-link">Interview</a></li>
          <li><a href="#contact" class="nav-link">Contact</a></li>
        </ul>
      </nav>

      <div class="nav-actions">
        <?php if ($loggedIn): ?>
          <div class="auth-user-pill" id="authUserPill" title="Logged in as <?= $authUser ?>">
            <span class="auth-avatar"><?= strtoupper(substr($authUser, 0, 1)) ?></span>
            <span class="auth-username"><?= $authUser ?></span>
          </div>
          <a href="index.php?logout=1" class="auth-logout-btn" id="logoutBtn" onclick="handleUserLogout(event)" title="Log Out" aria-label="Log Out">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            <span>Logout</span>
          </a>
        <?php else: ?>
          <button class="btn btn-outline-red btn-sm" id="loginNavBtn" onclick="openAuthModal('login')">
            Login
          </button>
          <button class="btn btn-primary btn-sm" id="registerNavBtn" onclick="openAuthModal('register')">
            Register
          </button>
        <?php endif; ?>
        <button class="mobile-toggle" aria-label="Toggle Navigation">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>
      </div>
    </div>
  </header>

  <!-- ==========================================================
       Hero Section
       ========================================================== -->
  <section class="hero-section" id="hero">
    <div class="container hero-grid">
      <div class="hero-content">
        <div class="hero-badge-pill">
          <span class="pulse-indicator red"></span>
          <span class="hero-badge-text"><?= htmlspecialchars($profile['status']) ?></span>
        </div>

        <h1 class="hero-title">
          Hi, I'm <span class="text-gradient">Ralph Tuliao</span>.<br>
          Building <span class="red-text">Next-Gen Web</span> & <span class="text-gradient">Smart Hardware</span>.
        </h1>

        <p class="hero-bio">
          <?= htmlspecialchars($profile['headline']) ?> I architect full-stack web platforms and craft responsive IoT systems that solve real problems with precision and aesthetic mastery.
        </p>

        <div class="hero-cta-group">
          <a href="#projects" class="btn btn-primary">
            <span>Explore Projects</span>
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </a>
          <a href="#interview" class="btn btn-video-play" onclick="playMockInterview(event)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 4 20 12 6 20 6 4"></polygon></svg>
            <span>Watch Interview</span>
          </a>
          <button type="button" class="btn btn-outline-red" id="heroResumeBtn" data-action="open-resume" onclick="openResumeModal(event)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            <span>Quick View Resume</span>
          </button>
          <a href="#contact" class="btn btn-secondary">
            <span>Get In Touch</span>
          </a>
        </div>

        <div class="hero-stats">
          <div class="stat-item">
            <div class="stat-num">03<span>+</span></div>
            <div class="stat-label">Core Flagships</div>
          </div>
          <div class="stat-item" style="cursor: pointer;" onclick="document.querySelector('#projects').scrollIntoView({behavior: 'smooth'})" title="Explore live interactive demos below">
            <div class="stat-num" style="color: #00ff88;">100<span>%</span></div>
            <div class="stat-label">Live Demos Ready</div>
          </div>
          <div class="stat-item">
            <div class="stat-num">24<span>/7</span></div>
            <div class="stat-label">Tech Dedication</div>
          </div>
        </div>
      </div>

      <!-- Hero Visual Side -->
      <div class="hero-visual-wrapper">
        <div class="profile-card-glow"></div>
        
        <!-- Floating Glass Badges -->
        <div class="floating-glass-badge badge-top-right">
          <div class="floating-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="16 18 22 12 16 6"></polyline>
              <polyline points="8 6 2 12 8 18"></polyline>
            </svg>
          </div>
          <div>
            <div class="floating-text-primary">Full-Stack Dev</div>
            <div class="floating-text-sub">PHP • MySQL • JS</div>
          </div>
        </div>

        <div class="floating-glass-badge badge-bottom-left">
          <div class="floating-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
              <rect x="9" y="9" width="6" height="6"></rect>
              <line x1="9" y1="1" x2="9" y2="4"></line>
              <line x1="15" y1="1" x2="15" y2="4"></line>
              <line x1="9" y1="20" x2="9" y2="23"></line>
              <line x1="15" y1="20" x2="15" y2="23"></line>
              <line x1="20" y1="9" x2="23" y2="9"></line>
              <line x1="20" y1="14" x2="23" y2="14"></line>
              <line x1="1" y1="9" x2="4" y2="9"></line>
              <line x1="1" y1="14" x2="4" y2="14"></line>
            </svg>
          </div>
          <div>
            <div class="floating-text-primary">Arduino & IoT</div>
            <div class="floating-text-sub">Microcontrollers & C++</div>
          </div>
        </div>

        <div class="profile-card">
          <div class="profile-image-container">
            <img src="<?= htmlspecialchars($profile['avatar']) ?>" alt="<?= htmlspecialchars($profile['full_name']) ?>" class="profile-image" loading="eager">
          </div>
          <div class="profile-info-bar">
            <div class="profile-name-tag">
              <h3><?= htmlspecialchars($profile['full_name']) ?></h3>
              <span class="status-badge">
                <span class="pulse-indicator"></span> Active
              </span>
            </div>
            <p class="profile-role-text"><?= htmlspecialchars($profile['title']) ?></p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================
       Featured Projects Section
       ========================================================== -->
  <section class="projects-section" id="projects">
    <div class="container">
      <div class="projects-header-row">
        <div>
          <div class="section-tag">
            <span class="dot"></span>
            <span>Featured Portfolio</span>
          </div>
          <h2 class="section-title">Showcase of <span class="text-gradient">Engineered Work</span></h2>
          <p class="section-subtitle">
            A curated selection of real-world web platforms and smart hardware implementations built for performance, aesthetics, and reliability.
          </p>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
          <button class="filter-btn active" data-filter="all">All Projects</button>
          <button class="filter-btn" data-filter="web">Web Apps</button>
          <button class="filter-btn" data-filter="iot">IoT & Hardware</button>
        </div>
      </div>

      <!-- Project Cards Grid -->
      <div class="projects-grid">
        <?php foreach ($projects as $project): 
          $demoLink = !empty($project['live_demo_url']) ? $project['live_demo_url'] : (!empty($project['demo_url']) && $project['demo_url'] !== '#' ? $project['demo_url'] : '');
        ?>
          <div class="project-card" data-category="<?= htmlspecialchars($project['category']) ?>" id="card-project-<?= $project['id'] ?>">
            <div class="project-thumb-wrapper">
              <span class="project-category-badge"><?= strtoupper(htmlspecialchars($project['category'])) ?></span>
              
              <?php if (!empty($demoLink)): ?>
                <button type="button" class="project-demo-badge" onclick="event.stopPropagation(); openLiveDemo('<?= htmlspecialchars($demoLink, ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($project['title']), ENT_QUOTES) ?>')" title="Click to launch interactive live demo">
                  <span class="live-demo-pulse"></span>
                  <span>Live Demo</span>
                </button>
              <?php endif; ?>

              <img src="<?= htmlspecialchars($project['image']) ?>" alt="<?= htmlspecialchars($project['title']) ?>" class="project-thumb" loading="lazy">
              
              <div class="project-overlay-action">
                <?php if (!empty($demoLink)): ?>
                  <button type="button" class="btn btn-primary btn-sm" onclick="openLiveDemo('<?= htmlspecialchars($demoLink, ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($project['title']), ENT_QUOTES) ?>')" title="Launch live interactive demo">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                    <span>Live Demo</span>
                  </button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary btn-sm" onclick="openProjectModal(<?= $project['id'] ?>)">
                  <span>Details</span>
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                  </svg>
                </button>
              </div>
            </div>

            <div class="project-body">
              <div class="project-subtitle"><?= htmlspecialchars($project['subtitle']) ?></div>
              <h3 class="project-title"><?= htmlspecialchars($project['title']) ?></h3>
              <p class="project-desc"><?= htmlspecialchars($project['description']) ?></p>

              <div class="project-tech-pills">
                <?php 
                  $tags = explode(',', $project['tags']);
                  foreach (array_slice($tags, 0, 4) as $tag): 
                ?>
                  <span class="tech-pill"><?= htmlspecialchars(trim($tag)) ?></span>
                <?php endforeach; ?>
              </div>

              <div class="project-footer">
                <button type="button" class="view-details-btn" onclick="openProjectModal(<?= $project['id'] ?>)">
                  <span>Explore Architecture</span>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                  </svg>
                </button>
                <?php if (!empty($demoLink)): ?>
                  <button type="button" class="btn btn-primary btn-sm live-demo-card-btn" onclick="openLiveDemo('<?= htmlspecialchars($demoLink, ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($project['title']), ENT_QUOTES) ?>')" title="Launch live interactive demo">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                    <span>Live Demo</span>
                  </button>
                <?php else: ?>
                  <button type="button" class="btn btn-outline-red btn-sm" onclick="openProjectModal(<?= $project['id'] ?>)">
                    Details
                  </button>
                <?php endif; ?>
              </div>
            </div>

            <!-- Embedded JSON Data for Modal -->
            <script type="application/json" id="project-data-<?= $project['id'] ?>">
              <?= json_encode($project, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
            </script>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ==========================================================
       Skills Section
       ========================================================== -->
  <section class="skills-section" id="skills">
    <div class="container">
      <div class="section-tag">
        <span class="dot"></span>
        <span>Capabilities</span>
      </div>
      <h2 class="section-title">Technical <span class="text-gradient">Arsenal</span></h2>
      <p class="section-subtitle">
        Bridging the divide between high-performance software code and tangible physical hardware systems.
      </p>

      <div class="skills-categories-grid">
        <?php foreach ($skillsByCategory as $categoryName => $skillList): ?>
          <div class="skill-category-card">
            <div class="skill-cat-header">
              <div class="skill-cat-icon">
                <?php if (strpos($categoryName, 'Frontend') !== false): ?>
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                  </svg>
                <?php elseif (strpos($categoryName, 'Backend') !== false): ?>
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                  </svg>
                <?php elseif (strpos($categoryName, 'IoT') !== false): ?>
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                  </svg>
                <?php else: ?>
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                  </svg>
                <?php endif; ?>
              </div>
              <h3 class="skill-cat-title"><?= htmlspecialchars($categoryName) ?></h3>
            </div>

            <div class="skills-list">
              <?php foreach ($skillList as $skill): ?>
                <div class="skill-item-row">
                  <div class="skill-info">
                    <span class="skill-name"><?= htmlspecialchars($skill['name']) ?></span>
                    <span class="skill-percent"><?= $skill['proficiency'] ?>%</span>
                  </div>
                  <div class="skill-bar-track">
                    <div class="skill-bar-fill" style="width: <?= $skill['proficiency'] ?>%;"></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ==========================================================
       About Me Section
       ========================================================== -->
  <section class="about-section" id="about">
    <div class="container">
      <div class="about-card">
        <div class="about-text-content">
          <div class="section-tag">
            <span class="dot"></span>
            <span>About Ralph</span>
          </div>
          <h2 class="section-title">Driven by <span class="red-text">Code</span>, Powered by <span class="text-gradient">Innovation</span></h2>
          <p>
            <?= htmlspecialchars($profile['bio']) ?>
          </p>
          <p>
            Whether implementing database schemas in MySQL, designing modern responsive user interfaces with crimson and dark palettes, or breadboarding microcontroller circuits for automatic dispensing systems, I aim for solutions that are both technically robust and user-friendly.
          </p>

          <div class="about-highlights">
            <div class="highlight-box">
              <div class="highlight-icon">⚡</div>
              <div class="highlight-title">Full-Stack Fluency</div>
              <div class="highlight-desc">End-to-end web apps from backend database architecture to polished frontend interfaces.</div>
            </div>
            <div class="highlight-box">
              <div class="highlight-icon">🔌</div>
              <div class="highlight-title">Hardware & Prototyping</div>
              <div class="highlight-desc">Hands-on experience with Arduino, sensors, relay switching, and automated mechanisms.</div>
            </div>
          </div>
        </div>

        <div class="about-interactive-box">
          <div class="code-header">
            <div class="code-dots">
              <span class="code-dot dot-red"></span>
              <span class="code-dot dot-yellow"></span>
              <span class="code-dot dot-green"></span>
            </div>
            <span class="code-filename">RalphTuliao.profile.js</span>
          </div>

          <div class="terminal-code">
            <p><span class="code-keyword">const</span> <span class="code-var">developer</span> = {</p>
            <p>&nbsp;&nbsp;name: <span class="code-str">'Ralph Tuliao'</span>,</p>
            <p>&nbsp;&nbsp;role: <span class="code-str">'Full-Stack Web & IoT Engineer'</span>,</p>
            <p>&nbsp;&nbsp;coreProjects: [</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-str">'Tapseafood: Online Food Ordering'</span>,</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-str">'Hotel Online Booking'</span>,</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-str">'Arduino Water Dispenser'</span></p>
            <p>&nbsp;&nbsp;],</p>
            <p>&nbsp;&nbsp;stack: [<span class="code-str">'PHP'</span>, <span class="code-str">'MySQL'</span>, <span class="code-str">'JavaScript'</span>, <span class="code-str">'Arduino C++'</span>],</p>
            <p>&nbsp;&nbsp;status: <span class="code-func">() =></span> <span class="code-str">'Ready for collaborations'</span></p>
            <p>};</p>
          </div>

          <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="#contact" class="btn btn-outline-red btn-sm">Hire Ralph</a>
            <button type="button" class="btn btn-primary btn-sm" id="aboutResumeBtn" data-action="open-resume" onclick="openResumeModal(event)">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
              <span>View Resume</span>
            </button>
            <a href="#interview" class="btn btn-secondary btn-sm" onclick="playMockInterview(event)">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 4 20 12 6 20 6 4"></polygon></svg>
              <span>Watch Interview</span>
            </a>
            <a href="database.sql" download class="btn btn-secondary btn-sm" title="Download SQL schema">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span>Download database.sql</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================
       Technical Mock Interview & Video Showcase Section
       ========================================================== -->
  <section class="mock-interview-section" id="interview">
    <div class="container">
      <div class="section-header text-center">
        <div class="section-tag" style="justify-content: center;">
          <span class="dot"></span>
          <span>Candidate Spotlight</span>
        </div>
        <h2 class="section-title">Technical <span class="red-text">Mock Interview</span> Video</h2>
        
      </div>

      <div class="interview-showcase-card">
        <!-- Top Window Bar -->
        <div class="interview-window-topbar">
          <div class="window-dots">
            <span class="dot red"></span>
            <span class="dot yellow"></span>
            <span class="dot green"></span>
          </div>
          <div class="interview-file-pill">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
            <span>ralph.mp4 • Candidate Technical Presentation</span>
          </div>
          <div class="interview-quality-badge">
            <span class="pulse-indicator red"></span>
            <span>Live Recording • MP4</span>
          </div>
        </div>

        <!-- Video Player Wrapper -->
        <div class="interview-player-container">
          <div class="interview-video-wrapper" id="interviewVideoWrapper">
            <video 
              id="mockInterviewVideo" 
              class="interview-video-element" 
              controls 
              preload="metadata" 
              poster="assets/images/ralph-tuliao.jpg"
              playsinline>
              <source src="ralph.mp4" type="video/mp4">
              Your browser does not support HTML5 video.
            </video>
    </div>
  </section>
  <!-- ==========================================================
       Contact Section
       ========================================================== -->
  <section class="contact-section" id="contact">
    <div class="container">
      <div class="contact-grid">
        <div class="contact-info-card">
          <div class="section-tag">
            <span class="dot"></span>
            <span>Get in Touch</span>
          </div>
          <h2 class="section-title">Let's Build Something <span class="red-text">Extraordinary</span></h2>
          <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Have a project in mind, want to collaborate on a web application, or discuss an IoT solution? Drop a message below and I will respond promptly.
          </p>

          <div class="contact-methods">
            <div class="contact-item">
              <div class="contact-icon-box">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                  <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
              </div>
              <div>
                <div class="contact-label">Email Directly</div>
                <div class="contact-val"><?= htmlspecialchars($profile['email']) ?></div>
              </div>
            </div>

            <div class="contact-item">
              <div class="contact-icon-box">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
              </div>
              <div>
                <div class="contact-label">Location</div>
                <div class="contact-val"><?= htmlspecialchars($profile['location']) ?></div>
              </div>
            </div>

            <div class="contact-item">
              <div class="contact-icon-box">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </div>
              <div>
                <div class="contact-label">Response Time</div>
                <div class="contact-val">Within 24 Hours</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-card">
          <form id="contactForm" method="POST" action="api_contact.php">
            <div class="form-group">
              <label for="contactName" class="form-label">Your Full Name *</label>
              <input type="text" id="contactName" name="name" class="form-control" placeholder="" required>
            </div>

            <div class="form-group">
              <label for="contactEmail" class="form-label">Email Address *</label>
              <input type="email" id="contactEmail" name="email" class="form-control" placeholder="" required>
            </div>

            <div class="form-group">
              <label for="contactSubject" class="form-label">Inquiry Subject</label>
              <select id="contactSubject" name="subject" class="form-control">
                <option value="Web Development Project">Web Development Project (e.g. E-Commerce / Booking)</option>
                <option value="IoT & Hardware System">IoT & Smart Hardware Prototype</option>
                <option value="Full-Stack Collaboration">Full-Stack Collaboration</option>
                <option value="General Question">General Inquiry</option>
              </select>
            </div>

            <div class="form-group">
              <label for="contactMessage" class="form-label">Your Message *</label>
              <textarea id="contactMessage" name="message" class="form-control" placeholder="Tell me about your project goals, scope, and timeline..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
              <span>Send Message to Ralph</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
              </svg>
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================
       Project Details Modal Dialog
       ========================================================== -->
  <div class="modal-backdrop" id="projectModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-window">
      <button class="modal-close-btn" id="modalCloseBtn" aria-label="Close dialog">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <div class="modal-banner">
        <img id="modalImage" src="" alt="Project Preview">
      </div>

      <div class="modal-content-body">
        <div class="modal-tag" id="modalCategory">WEB PROJECT</div>
        <h3 class="modal-title" id="modalTitle">Project Title</h3>
        <p class="modal-subtitle" id="modalSubtitle">Subheading</p>

        <p id="modalDescription" style="color: var(--text-secondary); line-height: 1.7; font-size: 1.02rem;"></p>

        <h4 class="modal-section-title">Key Architectural Features</h4>
        <ul class="feature-list" id="modalFeatures"></ul>

        <h4 class="modal-section-title">Technologies & Components</h4>
        <div class="project-tech-pills" id="modalTechPills"></div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
          <button type="button" id="modalLiveDemoBtn" class="btn btn-primary" onclick="openLiveDemoFromModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
            <span>Live Demo</span>
          </button>
          <a id="modalGithubBtn" href="#" target="_blank" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
            </svg>
            <span>Source Code</span>
          </a>
          <a id="modalLiveBtn" href="#contact" class="btn btn-outline-red" onclick="document.getElementById('modalCloseBtn').click();">
            <span>Inquire</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <div class="toast-notice" id="toastNotice">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--red-primary)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
      <polyline points="22 4 12 14.01 9 11.01"></polyline>
    </svg>
    <span id="toastMessage">Message sent successfully!</span>
  </div>

  <!-- ==========================================================
       Footer
       ========================================================== -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="brand-monogram">
            <img src="<?= htmlspecialchars($brandLogo) ?>" alt="<?= htmlspecialchars($profile['full_name']) ?>" class="brand-logo-img">
          </div>
          <div>
            <h4 style="font-size: 1.1rem; color: #fff;">Ralph Tuliao</h4>
            <p style="font-size: 0.8rem; color: var(--text-muted);">Full-Stack Web & IoT Engineering</p>
          </div>
        </div>

        <ul class="footer-nav">
          <li><a href="#hero">Home</a></li>
          <li><a href="#projects">Projects</a></li>
          <li><a href="#skills">Skills</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#interview">Interview</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>

        <div class="footer-socials">
          <a href="https://github.com/ralphtuliao" target="_blank" rel="noopener" class="social-link" aria-label="GitHub" title="GitHub">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
          </a>
          <a href="https://linkedin.com/in/ralphtuliao" target="_blank" rel="noopener" class="social-link" aria-label="LinkedIn" title="LinkedIn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
          </a>
          <a href="mailto:ralphtuliao.tuliao@gmail.com" class="social-link" aria-label="Email" title="Send Email">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
          </a>
        </div>
      </div>

      <div class="footer-bottom">
        <div>&copy; <?= date('Y') ?> Ralph Tuliao. All Rights Reserved. Crafted with Red & Black Modern Design.</div>
        <div>
          <?php if ($db_connected): ?>
            <span class="db-status-pill connected" title="MySQL Connected to ralph_portfolio">
              <span class="pulse-indicator"></span> MySQL Connected
            </span>
          <?php else: ?>
            <span class="db-status-pill fallback" title="Using db.php resilient fallback data. Import database.sql into phpMyAdmin/MySQL to activate live DB mode.">
              <span class="pulse-indicator red"></span> Ready for DB (database.sql included)
            </span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </footer>

  <!-- ============================================================
       Resume Quick View Modal — Matches Actual CV
       ============================================================ -->
  <!-- ============================================================
       Resume Quick View Modal — Actual Image Popup of Ralph S. Tuliao CV
       ============================================================ -->
  <div class="modal-backdrop" id="resumeModal" role="dialog" aria-modal="true" aria-labelledby="resumeModalTitle">
    <div class="modal-window resume-modal-window">
      <!-- Dark Topbar with Document Actions -->
      <div class="cv-topbar">
        <div class="cv-topbar-left">
          <span class="cv-badge">OFFICIAL RESUME</span>
          <span class="cv-doc-name" id="resumeModalTitle">Ralph_S_Tuliao_Resume.png</span>
        </div>
        <div class="cv-topbar-actions">
          <button type="button" class="btn btn-outline-red btn-sm" id="cvZoomToggleBtn" onclick="toggleResumeZoom()" title="Toggle full size zoom">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
            <span id="cvZoomLabel">Zoom In</span>
          </button>
          <a href="<?= htmlspecialchars($resumeImage) ?>?v=<?= $resumeVersion ?>" target="_blank" class="btn btn-outline-red btn-sm" title="Open high-res original in new tab">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            <span>Open Original</span>
          </a>
          <button type="button" class="btn btn-outline-red btn-sm" onclick="printResume()" title="Print CV">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            <span>Print</span>
          </button>
          <a href="<?= htmlspecialchars($resumeImage) ?>" download="Ralph-Tuliao-Resume.png" class="btn btn-primary btn-sm" id="downloadResumeBtn" title="Download official resume image">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Download</span>
          </a>
          <button class="modal-close-btn" id="resumeModalClose" aria-label="Close resume" onclick="closeResumeModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        </div>
      </div>

      <!-- Scrollable Document Viewport with Image Pop Up -->
      <div class="cv-viewport" id="cvViewport">
        <div class="cv-image-card" id="cvPaperSheet">
          <img src="<?= htmlspecialchars($resumeImage) ?>?v=<?= $resumeVersion ?>" alt="Ralph S. Tuliao Resume" class="cv-popup-image" id="cvPopupImg" onclick="toggleResumeZoom()">
          <div class="cv-zoom-hint">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
            <span>Click image or zoom button to toggle full size</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
       Live Demo Modal (iframe embed)
       ============================================================ -->
  <div class="modal-backdrop" id="liveDemoModal" role="dialog" aria-modal="true" aria-labelledby="liveDemoTitle">
    <div class="modal-window live-demo-modal-window">
      <div class="live-demo-topbar">
        <div class="live-demo-title-row">
          <span class="live-demo-pulse"></span>
          <h3 id="liveDemoTitle" class="live-demo-title">Live Demo</h3>
        </div>
        <div class="live-demo-controls">
          <a id="liveDemoOpenTab" href="#" target="_blank" class="btn btn-outline-red btn-sm">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            Open in Tab
          </a>
          <button class="modal-close-btn" id="liveDemoClose" aria-label="Close demo">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        </div>
      </div>
      <div class="live-demo-frame-wrapper">
        <div class="live-demo-loader" id="liveDemoLoader">
          <div class="live-demo-spinner"></div>
          <p>Loading live demo...</p>
        </div>
        <iframe id="liveDemoFrame" src="" frameborder="0" allowfullscreen title="Live Demo Preview"></iframe>
      </div>
    </div>
  </div>

  <!-- ============================================================
       Auth Modal (Login / Register)
       ============================================================ -->
  <div class="modal-backdrop" id="authModal" role="dialog" aria-modal="true" aria-labelledby="authModalTitle">
    <div class="modal-window auth-modal-window">
      <button class="modal-close-btn" id="authModalClose" aria-label="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>

      <!-- Tab Switcher -->
      <div class="auth-tabs">
        <button class="auth-tab active" id="authTabLogin" onclick="switchAuthTab('login')">Login</button>
        <button class="auth-tab" id="authTabRegister" onclick="switchAuthTab('register')">Register</button>
      </div>

      <!-- Auth Icon -->
      <div class="auth-icon-circle">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
      </div>
      <h2 class="auth-heading" id="authModalTitle">Welcome Back</h2>
      <p class="auth-subtext" id="authSubtext">Sign in to your account</p>

      <!-- Login Form -->
      <form class="auth-form" id="loginForm">
        <div class="form-group">
          <label for="loginIdentifier" class="form-label">Username or Email</label>
          <input type="text" id="loginIdentifier" name="identifier" class="form-control" placeholder="Enter username or email" required autocomplete="username">
        </div>
        <div class="form-group">
          <label for="loginPassword" class="form-label">Password</label>
          <div class="password-wrapper">
            <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
            <button type="button" class="password-toggle" onclick="togglePassword('loginPassword', this)" aria-label="Toggle password visibility">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </button>
          </div>
        </div>
        <div class="auth-error" id="loginError"></div>
        <button type="submit" class="btn btn-primary" style="width:100%;" id="loginSubmitBtn">
          <span>Sign In</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
        <p class="auth-switch">Don't have an account? <a href="javascript:void(0)" onclick="switchAuthTab('register')">Register here</a></p>
      </form>

      <!-- Register Form -->
      <form class="auth-form" id="registerForm" style="display:none;">
        <div class="form-group">
          <label for="regUsername" class="form-label">Username</label>
          <input type="text" id="regUsername" name="username" class="form-control" placeholder="Choose a username" required autocomplete="username" minlength="3" maxlength="50">
        </div>
        <div class="form-group">
          <label for="regEmail" class="form-label">Email Address</label>
          <input type="email" id="regEmail" name="email" class="form-control" placeholder="Your email address" required autocomplete="email">
        </div>
        <div class="form-group">
          <label for="regPassword" class="form-label">Password</label>
          <div class="password-wrapper">
            <input type="password" id="regPassword" name="password" class="form-control" placeholder="Min. 6 characters" required autocomplete="new-password" minlength="6">
            <button type="button" class="password-toggle" onclick="togglePassword('regPassword', this)" aria-label="Toggle password visibility">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </button>
          </div>
        </div>
        <div class="form-group">
          <label for="regPasswordConfirm" class="form-label">Confirm Password</label>
          <div class="password-wrapper">
            <input type="password" id="regPasswordConfirm" name="password_confirm" class="form-control" placeholder="Repeat your password" required autocomplete="new-password">
            <button type="button" class="password-toggle" onclick="togglePassword('regPasswordConfirm', this)" aria-label="Toggle">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </button>
          </div>
        </div>
        <div class="auth-error" id="registerError"></div>
        <button type="submit" class="btn btn-primary" style="width:100%;" id="registerSubmitBtn">
          <span>Create Account</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
        <p class="auth-switch">Already have an account? <a href="javascript:void(0)" onclick="switchAuthTab('login')">Login here</a></p>
      </form>
    </div>
  </div>

  <!-- Main JavaScript -->
  <script src="assets/js/main.js"></script>
</body>
</html>
