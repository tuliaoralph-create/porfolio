/**
 * Ralph Tuliao Portfolio - Modern Client Interactivity Script
 * Includes: Project Modal, Live Demo, Resume Modal, Auth (Login/Register), Filters
 */
// -----------------------------------------------------------------
// Immediate Global API (Always available before and after DOMContentLoaded)
// -----------------------------------------------------------------
window.openResumeModal = function(e) {
  if (e && e.preventDefault) e.preventDefault();
  const modal = document.getElementById('resumeModal');
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
};

window.closeResumeModal = function() {
  const modal = document.getElementById('resumeModal');
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
    const img = document.getElementById('cvPopupImg');
    const card = document.getElementById('cvPaperSheet');
    if (img) img.classList.remove('zoomed');
    if (card) card.classList.remove('zoomed');
    const label = document.getElementById('cvZoomLabel');
    if (label) label.textContent = 'Zoom In';
  }
};

window.toggleResumeZoom = function() {
  const img = document.getElementById('cvPopupImg');
  const card = document.getElementById('cvPaperSheet');
  const label = document.getElementById('cvZoomLabel');
  if (img) {
    const willZoom = !img.classList.contains('zoomed');
    img.classList.toggle('zoomed', willZoom);
    if (card) card.classList.toggle('zoomed', willZoom);
    if (label) {
      label.textContent = willZoom ? 'Fit to Screen' : 'Zoom In';
    }
  }
};

window.printResume = function() {
  window.print();
};

window.openAuthModal = function(tab) {
  const modal = document.getElementById('authModal');
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    if (typeof window.switchAuthTab === 'function') {
      window.switchAuthTab(tab || 'login');
    }
  }
};

window.closeAuthModal = function() {
  const modal = document.getElementById('authModal');
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
};

// -----------------------------------------------------------------
// Mock Interview Video Controls (ralph.mp4)
// -----------------------------------------------------------------
window.playMockInterview = function(e) {
  if (e && e.preventDefault) e.preventDefault();
  const section = document.getElementById('interview');
  if (section) {
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  const video = document.getElementById('mockInterviewVideo');
  if (video) {
    setTimeout(() => {
      video.play().catch(() => {});
      const overlay = document.getElementById('videoOverlayBanner');
      if (overlay) overlay.classList.add('playing');
    }, 450);
  }
};

window.toggleInterviewPlayback = function(forcePlay) {
  const video = document.getElementById('mockInterviewVideo');
  const overlay = document.getElementById('videoOverlayBanner');
  const playLabel = document.getElementById('ctrlPlayLabel');
  const playIcon = document.getElementById('ctrlPlayIcon');
  if (!video) return;

  if (forcePlay === true || video.paused) {
    video.play().then(() => {
      if (overlay) overlay.classList.add('playing');
      if (playLabel) playLabel.textContent = 'Pause';
      if (playIcon) playIcon.innerHTML = '<rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect>';
    }).catch(err => {
      console.log('Video autoplay notice:', err);
    });
  } else {
    video.pause();
    if (playLabel) playLabel.textContent = 'Play';
    if (playIcon) playIcon.innerHTML = '<polygon points="6 4 20 12 6 20 6 4"></polygon>';
  }
};

window.restartInterviewVideo = function() {
  const video = document.getElementById('mockInterviewVideo');
  if (video) {
    video.currentTime = 0;
    window.toggleInterviewPlayback(true);
  }
};

window.toggleInterviewMute = function() {
  const video = document.getElementById('mockInterviewVideo');
  const muteLabel = document.getElementById('ctrlMuteLabel');
  const muteIcon = document.getElementById('ctrlMuteIcon');
  if (!video) return;

  video.muted = !video.muted;
  if (muteLabel) muteLabel.textContent = video.muted ? 'Unmute' : 'Mute';
  if (muteIcon) {
    muteIcon.innerHTML = video.muted
      ? '<line x1="1" y1="1" x2="23" y2="23"></line><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>'
      : '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>';
  }
};

window.setInterviewSpeed = function(rate, btnEl) {
  const video = document.getElementById('mockInterviewVideo');
  if (video) {
    video.playbackRate = parseFloat(rate);
    document.querySelectorAll('.speed-chip').forEach(btn => btn.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');
  }
};

window.seekInterview = function(seconds) {
  const video = document.getElementById('mockInterviewVideo');
  if (video) {
    video.currentTime = seconds;
    window.toggleInterviewPlayback(true);
  }
};

window.toggleInterviewFullscreen = function() {
  const wrapper = document.getElementById('interviewVideoWrapper');
  const video = document.getElementById('mockInterviewVideo');
  const target = wrapper || video;
  if (!target) return;

  if (!document.fullscreenElement) {
    if (target.requestFullscreen) {
      target.requestFullscreen();
    } else if (target.webkitRequestFullscreen) {
      target.webkitRequestFullscreen();
    } else if (video && video.webkitEnterFullscreen) {
      video.webkitEnterFullscreen();
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    }
  }
};

document.addEventListener('DOMContentLoaded', () => {
  // Elements
  const header = document.querySelector('.site-header');
  const mobileToggle = document.querySelector('.mobile-toggle');
  const navLinks = document.querySelector('.nav-links');
  const filterBtns = document.querySelectorAll('.filter-btn');
  const projectCards = document.querySelectorAll('.project-card');
  const modalBackdrop = document.getElementById('projectModal');
  const modalCloseBtn = document.getElementById('modalCloseBtn');
  const contactForm = document.getElementById('contactForm');
  const toastNotice = document.getElementById('toastNotice');
  const toastMessage = document.getElementById('toastMessage');

  // -----------------------------------------------------------------
  // Back to Top Button
  // -----------------------------------------------------------------
  const backToTopBtn = document.getElementById('backToTopBtn');
  if (backToTopBtn) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 400) {
        backToTopBtn.classList.add('visible');
      } else {
        backToTopBtn.classList.remove('visible');
      }
    }, { passive: true });
    backToTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // -----------------------------------------------------------------
  // Cursor Glow Follower
  // -----------------------------------------------------------------
  const cursorGlow = document.getElementById('cursorGlow');
  if (cursorGlow && window.matchMedia('(hover: hover)').matches) {
    let mouseX = 0, mouseY = 0;
    let glowX = 0, glowY = 0;
    let rafId;

    document.addEventListener('mousemove', e => {
      mouseX = e.clientX;
      mouseY = e.clientY;
    }, { passive: true });

    function animateGlow() {
      glowX += (mouseX - glowX) * 0.08;
      glowY += (mouseY - glowY) * 0.08;
      cursorGlow.style.left = glowX + 'px';
      cursorGlow.style.top  = glowY + 'px';
      rafId = requestAnimationFrame(animateGlow);
    }
    animateGlow();
  }


  // -----------------------------------------------------------------
  // Sticky Header on Scroll
  // -----------------------------------------------------------------
  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
    highlightNavOnScroll();
  });

  // Mobile Navigation Toggle
  if (mobileToggle && navLinks) {
    mobileToggle.addEventListener('click', () => {
      navLinks.classList.toggle('open');
    });
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('open');
      });
    });
  }

  // Active Link Spy
  const sections = document.querySelectorAll('section[id]');
  function highlightNavOnScroll() {
    const scrollY = window.pageYOffset;
    sections.forEach(current => {
      const sectionHeight = current.offsetHeight;
      const sectionTop = current.offsetTop - 120;
      const sectionId = current.getAttribute('id');
      const correspondingLink = document.querySelector(`.nav-link[href*="${sectionId}"]`);
      if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        if (correspondingLink) correspondingLink.classList.add('active');
      }
    });
  }

  // -----------------------------------------------------------------
  // Project Category Filtering
  // -----------------------------------------------------------------
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.getAttribute('data-filter');

      projectCards.forEach(card => {
        const category = card.getAttribute('data-category');
        if (filter === 'all' || category === filter) {
          card.style.display = 'flex';
          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, 50);
        } else {
          card.style.opacity = '0';
          card.style.transform = 'translateY(15px)';
          setTimeout(() => {
            card.style.display = 'none';
          }, 250);
        }
      });
    });
  });

  // -----------------------------------------------------------------
  // Project Details Modal
  // -----------------------------------------------------------------
  const modalImage     = document.getElementById('modalImage');
  const modalCategory  = document.getElementById('modalCategory');
  const modalTitle     = document.getElementById('modalTitle');
  const modalSubtitle  = document.getElementById('modalSubtitle');
  const modalDescription = document.getElementById('modalDescription');
  const modalTechPills = document.getElementById('modalTechPills');
  const modalFeatures  = document.getElementById('modalFeatures');
  const modalLiveBtn   = document.getElementById('modalLiveBtn');
  const modalGithubBtn = document.getElementById('modalGithubBtn');

  // Store the current project's live_demo_url for the Live Demo button
  let currentProjectDemoUrl = '';
  let currentProjectTitle   = '';

  window.openProjectModal = function(projectId) {
    const projectElement = document.getElementById(`project-data-${projectId}`);
    if (!projectElement) return;

    try {
      const data = JSON.parse(projectElement.textContent);

      currentProjectDemoUrl = data.live_demo_url || '';
      currentProjectTitle   = data.title || 'Live Demo';

      modalImage.src           = data.image;
      modalImage.alt           = data.title;
      modalCategory.textContent = data.category.toUpperCase() + ' PROJECT';
      modalTitle.textContent   = data.title;
      modalSubtitle.textContent = data.subtitle;
      modalDescription.textContent = data.description;

      // Tech Stack
      modalTechPills.innerHTML = '';
      const techs = data.tech_stack.split(',').map(t => t.trim());
      techs.forEach(tech => {
        const span = document.createElement('span');
        span.className = 'tech-pill';
        span.textContent = tech;
        modalTechPills.appendChild(span);
      });

      // Features
      modalFeatures.innerHTML = '';
      const features = data.features.split(';').map(f => f.trim()).filter(Boolean);
      features.forEach(feature => {
        const li = document.createElement('li');
        li.className = 'feature-item';
        li.innerHTML = `
          <svg class="feature-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>${feature}</span>
        `;
        modalFeatures.appendChild(li);
      });

      // Action Links
      modalGithubBtn.href = data.github_url || '#';

      // Toggle Live Demo button visibility
      const liveDemoBtn = document.getElementById('modalLiveDemoBtn');
      if (liveDemoBtn) {
        if (currentProjectDemoUrl && currentProjectDemoUrl !== '' && !currentProjectDemoUrl.startsWith('#')) {
          liveDemoBtn.style.display = 'flex';
        } else {
          liveDemoBtn.style.display = 'none';
        }
      }

      // Show Modal
      modalBackdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
    } catch (e) {
      console.error('Error opening project details:', e);
    }
  };

  // Open live demo from inside the project modal
  window.openLiveDemoFromModal = function() {
    if (currentProjectDemoUrl) {
      closeModal();
      openLiveDemo(currentProjectDemoUrl, currentProjectTitle);
    }
  };

  function closeModal() {
    if (modalBackdrop) {
      modalBackdrop.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  if (modalCloseBtn) {
    modalCloseBtn.addEventListener('click', closeModal);
  }

  if (modalBackdrop) {
    modalBackdrop.addEventListener('click', e => {
      if (e.target === modalBackdrop) closeModal();
    });
  }

  window.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      closeModal();
      closeLiveDemoModal();
      closeResumeModal();
      closeAuthModal();
    }
  });

  // -----------------------------------------------------------------
  // Live Demo Modal
  // -----------------------------------------------------------------
  const liveDemoModal   = document.getElementById('liveDemoModal');
  const liveDemoFrame   = document.getElementById('liveDemoFrame');
  const liveDemoLoader  = document.getElementById('liveDemoLoader');
  const liveDemoTitle   = document.getElementById('liveDemoTitle');
  const liveDemoOpenTab = document.getElementById('liveDemoOpenTab');
  const liveDemoClose   = document.getElementById('liveDemoClose');

  window.openLiveDemo = function(url, title = 'Live Demo') {
    if (!liveDemoModal || !liveDemoFrame) return;
    liveDemoTitle.textContent = title + ' — Live Preview';
    liveDemoOpenTab.href = url;
    liveDemoFrame.src = '';
    liveDemoLoader.style.display = 'flex';
    liveDemoFrame.style.opacity = '0';
    liveDemoModal.classList.add('active');
    document.body.style.overflow = 'hidden';

    // Small delay then load iframe
    setTimeout(() => {
      liveDemoFrame.src = url;
    }, 200);

    liveDemoFrame.onload = () => {
      liveDemoLoader.style.display = 'none';
      liveDemoFrame.style.opacity = '1';
    };
  };

  function closeLiveDemoModal() {
    if (liveDemoModal) {
      liveDemoModal.classList.remove('active');
      if (liveDemoFrame) liveDemoFrame.src = '';
      document.body.style.overflow = '';
    }
  }

  if (liveDemoClose) liveDemoClose.addEventListener('click', closeLiveDemoModal);
  if (liveDemoModal) {
    liveDemoModal.addEventListener('click', e => {
      if (e.target === liveDemoModal) closeLiveDemoModal();
    });
  }

  // -----------------------------------------------------------------
  // -----------------------------------------------------------------
  // Resume Modal Wiring
  // -----------------------------------------------------------------
  const resumeModal      = document.getElementById('resumeModal');
  const resumeModalClose = document.getElementById('resumeModalClose');
  const heroResumeBtn    = document.getElementById('heroResumeBtn');
  const aboutResumeBtn   = document.getElementById('aboutResumeBtn');

  const cvViewport       = document.getElementById('cvViewport');

  if (resumeModalClose) resumeModalClose.addEventListener('click', window.closeResumeModal);
  if (heroResumeBtn)    heroResumeBtn.addEventListener('click', window.openResumeModal);
  if (aboutResumeBtn)   aboutResumeBtn.addEventListener('click', window.openResumeModal);

  if (resumeModal) {
    resumeModal.addEventListener('click', e => {
      if (e.target === resumeModal) window.closeResumeModal();
    });
  }

  if (cvViewport) {
    cvViewport.addEventListener('click', e => {
      if (e.target === cvViewport) window.closeResumeModal();
    });
  }

  // Document-wide delegation for any resume button click
  document.addEventListener('click', e => {
    const trigger = e.target.closest('#heroResumeBtn, #aboutResumeBtn, [data-action="open-resume"]');
    if (trigger) {
      e.preventDefault();
      window.openResumeModal();
    }
  });

  // -----------------------------------------------------------------
  // Auth Modal (Login / Register)
  // -----------------------------------------------------------------
  const authModal      = document.getElementById('authModal');
  const authModalClose = document.getElementById('authModalClose');
  const loginForm      = document.getElementById('loginForm');
  const registerForm   = document.getElementById('registerForm');
  const authHeading    = document.getElementById('authModalTitle');
  const authSubtext    = document.getElementById('authSubtext');
  const loginError     = document.getElementById('loginError');
  const registerError  = document.getElementById('registerError');

  window.openAuthModal = function(tab = 'login') {
    const authModal = document.getElementById('authModal');
    if (authModal) {
      authModal.classList.add('active');
      document.body.style.overflow = 'hidden';
      window.switchAuthTab(tab);
    }
  };

  window.closeAuthModal = function() {
    const authModal = document.getElementById('authModal');
    if (authModal) {
      authModal.classList.remove('active');
      document.body.style.overflow = '';
    }
  };

  if (authModalClose) authModalClose.addEventListener('click', window.closeAuthModal);
  if (authModal) {
    authModal.addEventListener('click', e => {
      if (e.target === authModal) window.closeAuthModal();
    });
  }

  window.switchAuthTab = function(tab) {
    const loginTab    = document.getElementById('authTabLogin');
    const registerTab = document.getElementById('authTabRegister');
    const loginForm   = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const authHeading = document.getElementById('authModalTitle');
    const authSubtext = document.getElementById('authSubtext');
    const loginError  = document.getElementById('loginError');
    const registerError = document.getElementById('registerError');

    if (tab === 'login') {
      if (loginForm) loginForm.style.display    = 'flex';
      if (registerForm) registerForm.style.display = 'none';
      if (loginTab) loginTab.classList.add('active');
      if (registerTab) registerTab.classList.remove('active');
      if (authHeading) authHeading.textContent = 'Welcome Back';
      if (authSubtext) authSubtext.textContent = 'Sign in to your account';
    } else {
      if (loginForm) loginForm.style.display    = 'none';
      if (registerForm) registerForm.style.display = 'flex';
      if (loginTab) loginTab.classList.remove('active');
      if (registerTab) registerTab.classList.add('active');
      if (authHeading) authHeading.textContent = 'Create Account';
      if (authSubtext) authSubtext.textContent = 'Join Ralph\'s portfolio community';
    }
    if (loginError)   loginError.textContent   = '';
    if (registerError) registerError.textContent = '';
  };

  // Login Form Submit
  if (loginForm) {
    loginForm.addEventListener('submit', async e => {
      e.preventDefault();
      const btn = document.getElementById('loginSubmitBtn');
      btn.disabled = true;
      btn.innerHTML = '<span>Signing in...</span>';
      loginError.textContent = '';

      const formData = new FormData(loginForm);
      formData.append('action', 'login');

      try {
        const res = await fetch('api_auth.php', { method: 'POST', body: formData });
        const rawText = await res.text();
        let data;
        try {
          data = JSON.parse(rawText);
        } catch (_) {
          throw new Error('Invalid response from server.');
        }

        if (data.success) {
          showToast(`Welcome back, ${data.username}! 🎉`);
          window.closeAuthModal();
          setTimeout(() => location.reload(), 700);
        } else {
          loginError.textContent = data.error || 'Login failed.';
          shakeElement(loginError);
        }
      } catch (err) {
        loginError.textContent = err.message || 'Network error. Please try again.';
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>Sign In</span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
      }
    });
  }

  // Register Form Submit
  if (registerForm) {
    registerForm.addEventListener('submit', async e => {
      e.preventDefault();
      const btn = document.getElementById('registerSubmitBtn');
      registerError.textContent = '';

      const password = document.getElementById('regPassword').value;
      const confirm  = document.getElementById('regPasswordConfirm').value;
      if (password !== confirm) {
        registerError.textContent = 'Passwords do not match.';
        shakeElement(registerError);
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<span>Creating account...</span>';

      const formData = new FormData(registerForm);
      formData.append('action', 'register');

      try {
        const res = await fetch('api_auth.php', { method: 'POST', body: formData });
        const rawText = await res.text();
        let data;
        try {
          data = JSON.parse(rawText);
        } catch (_) {
          throw new Error('Invalid response from server.');
        }

        if (data.success) {
          showToast(`Account created! Welcome, ${data.username}! 🎉`);
          window.closeAuthModal();
          setTimeout(() => location.reload(), 700);
        } else {
          registerError.textContent = data.error || 'Registration failed.';
          shakeElement(registerError);
        }
      } catch (err) {
        registerError.textContent = err.message || 'Network error. Please try again.';
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>Create Account</span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
      }
    });
  }

  // Logout Button & Global Handler
  window.handleUserLogout = async function(e) {
    if (e && e.preventDefault) e.preventDefault();
    const btn = document.getElementById('logoutBtn');
    if (btn) {
      btn.style.pointerEvents = 'none';
      btn.style.opacity = '0.5';
    }
    try {
      const formData = new FormData();
      formData.append('action', 'logout');
      await fetch('api_auth.php', { method: 'POST', body: formData });
    } catch (_) {}
    showToast('Logged out successfully.');
    setTimeout(() => {
      window.location.href = 'index.php?logout=1';
    }, 350);
  };

  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', window.handleUserLogout);
  }

  // Password Show/Hide Toggle
  window.togglePassword = function(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    if (field.type === 'password') {
      field.type = 'text';
      btn.style.color = 'var(--red-primary)';
    } else {
      field.type = 'password';
      btn.style.color = '';
    }
  };

  // -----------------------------------------------------------------
  // Contact Form AJAX Handler
  // -----------------------------------------------------------------
  if (contactForm) {
    contactForm.addEventListener('submit', async e => {
      e.preventDefault();
      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = `<span>Sending...</span>`;
      submitBtn.disabled = true;

      const formData = new FormData(contactForm);

      try {
        const response = await fetch('api_contact.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        if (result.success) {
          showToast(result.message || 'Message sent successfully! Ralph will contact you soon.');
          contactForm.reset();
        } else {
          showToast(result.error || 'Failed to send message. Please try again.', true);
        }
      } catch (err) {
        showToast('Thank you! Your message has been received.', false);
        contactForm.reset();
      } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    });
  }

  // -----------------------------------------------------------------
  // Toast Notification
  // -----------------------------------------------------------------
  function showToast(message, isError = false) {
    if (!toastNotice || !toastMessage) return;
    toastMessage.textContent = message;
    toastNotice.style.borderColor = isError ? '#ff4d4d' : 'var(--red-primary)';
    toastNotice.classList.add('show');
    setTimeout(() => toastNotice.classList.remove('show'), 4500);
  }

  // Shake helper for form error feedback
  function shakeElement(el) {
    el.style.animation = 'none';
    el.offsetHeight; // reflow
    el.style.animation = 'authShake 0.4s ease';
    setTimeout(() => { el.style.animation = ''; }, 400);
  }

  // -----------------------------------------------------------------
  // Scroll-Reveal Animation System (IntersectionObserver)
  // -----------------------------------------------------------------
  const revealEls = document.querySelectorAll(
    '.project-card, .skill-category-card, .about-card, .contact-info-card, .contact-form-card, .highlight-box, .skill-item-row, .resume-section'
  );

  if (revealEls.length) {
    // Add initial hidden state
    revealEls.forEach((el, i) => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(28px)';
      el.style.transition = `opacity 0.6s cubic-bezier(0.16,1,0.3,1) ${(i % 4) * 0.08}s, transform 0.6s cubic-bezier(0.16,1,0.3,1) ${(i % 4) * 0.08}s`;
    });

    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    revealEls.forEach(el => revealObserver.observe(el));
  }

  // -----------------------------------------------------------------
  // Skill Bar Fill Animation on Scroll
  // -----------------------------------------------------------------
  const skillBars = document.querySelectorAll('.skill-bar-fill');
  skillBars.forEach(bar => {
    const targetWidth = bar.style.width;
    bar.style.width = '0%';
    bar.dataset.target = targetWidth;
  });

  const skillObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.querySelectorAll('.skill-bar-fill').forEach(bar => {
          setTimeout(() => {
            bar.style.width = bar.dataset.target;
          }, 120);
        });
        skillObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  document.querySelectorAll('.skill-category-card').forEach(card => {
    skillObserver.observe(card);
  });

  // -----------------------------------------------------------------
  // Hero Typewriter / Role Cycling
  // -----------------------------------------------------------------
  const heroTitleSpan = document.querySelector('.hero-title .red-text');
  if (heroTitleSpan) {
    const roles = ['Next-Gen Web', 'Smart IoT Systems', 'Elegant Solutions', 'Scalable Platforms'];
    let roleIndex = 0;
    let charIndex = 0;
    let deleting = false;
    let typeTimer;

    function typeRole() {
      const currentRole = roles[roleIndex];
      if (!deleting) {
        heroTitleSpan.textContent = currentRole.slice(0, charIndex + 1);
        charIndex++;
        if (charIndex === currentRole.length) {
          deleting = true;
          typeTimer = setTimeout(typeRole, 2200);
          return;
        }
      } else {
        heroTitleSpan.textContent = currentRole.slice(0, charIndex - 1);
        charIndex--;
        if (charIndex === 0) {
          deleting = false;
          roleIndex = (roleIndex + 1) % roles.length;
          typeTimer = setTimeout(typeRole, 350);
          return;
        }
      }
      typeTimer = setTimeout(typeRole, deleting ? 55 : 95);
    }

    // Start after a short delay so page loads first
    setTimeout(typeRole, 1200);
  }

  // -----------------------------------------------------------------
  // Project Card 3D Tilt Effect
  // -----------------------------------------------------------------
  document.querySelectorAll('.project-card').forEach(card => {
    card.addEventListener('mousemove', e => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;
      const rotateX = ((y - centerY) / centerY) * -4;
      const rotateY = ((x - centerX) / centerX) * 4;
      card.style.transform = `perspective(900px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = 'perspective(900px) rotateX(0) rotateY(0) translateY(0)';
    });
  });

  // -----------------------------------------------------------------
  // Animated Counter Stats in Hero
  // -----------------------------------------------------------------
  const statNums = document.querySelectorAll('.stat-num');
  statNums.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(14px)';
    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    setTimeout(() => {
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    }, 800);
  });

  // -----------------------------------------------------------------
  // Mock Interview Video Event Listeners
  // -----------------------------------------------------------------
  const interviewVid = document.getElementById('mockInterviewVideo');
  const overlayBanner = document.getElementById('videoOverlayBanner');
  const ctrlPlayLabel = document.getElementById('ctrlPlayLabel');
  const ctrlPlayIcon = document.getElementById('ctrlPlayIcon');

  if (interviewVid) {
    interviewVid.addEventListener('play', () => {
      if (overlayBanner) overlayBanner.classList.add('playing');
      if (ctrlPlayLabel) ctrlPlayLabel.textContent = 'Pause';
      if (ctrlPlayIcon) ctrlPlayIcon.innerHTML = '<rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect>';
    });

    interviewVid.addEventListener('pause', () => {
      if (ctrlPlayLabel) ctrlPlayLabel.textContent = 'Play';
      if (ctrlPlayIcon) ctrlPlayIcon.innerHTML = '<polygon points="6 4 20 12 6 20 6 4"></polygon>';
    });

    interviewVid.addEventListener('ended', () => {
      if (overlayBanner) overlayBanner.classList.remove('playing');
      if (ctrlPlayLabel) ctrlPlayLabel.textContent = 'Replay';
      if (ctrlPlayIcon) ctrlPlayIcon.innerHTML = '<polygon points="6 4 20 12 6 20 6 4"></polygon>';
    });
  }

});
