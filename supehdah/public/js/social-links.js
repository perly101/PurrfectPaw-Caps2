/**
 * Social Media Links Configuration
 * Update your social media links here and they will be automatically updated on the website
 */

const purrfectPawSocials = {
    // Update these URLs with your actual social media profiles
    facebook: "https://www.facebook.com/pearlgenepetallo.clava/",
    twitter: "https://twitter.com/purrfectpaw",
    instagram: "https://www.instagram.com/purrfectpaw",
    youtube: "https://www.youtube.com/channel/purrfectpaw",
    email: "purrf3ctpaw@gmail.com"
};

// This function updates all social media links on the page
function updateSocialMediaLinks() {
    // Update Facebook link
    const facebookLinks = document.querySelectorAll('a[data-social="facebook"]');
    facebookLinks.forEach(link => {
        link.href = purrfectPawSocials.facebook;
    });

    // Update Twitter link
    const twitterLinks = document.querySelectorAll('a[data-social="twitter"]');
    twitterLinks.forEach(link => {
        link.href = purrfectPawSocials.twitter;
    });

    // Update Instagram link
    const instagramLinks = document.querySelectorAll('a[data-social="instagram"]');
    instagramLinks.forEach(link => {
        link.href = purrfectPawSocials.instagram;
    });

    // Update YouTube link
    const youtubeLinks = document.querySelectorAll('a[data-social="youtube"]');
    youtubeLinks.forEach(link => {
        link.href = purrfectPawSocials.youtube;
    });

    // Update Email link
    const emailLinks = document.querySelectorAll('a[data-social="email"]');
    emailLinks.forEach(link => {
        link.href = `mailto:${purrfectPawSocials.email}`;
    });
}

// Run the update function when the page loads
document.addEventListener('DOMContentLoaded', updateSocialMediaLinks);