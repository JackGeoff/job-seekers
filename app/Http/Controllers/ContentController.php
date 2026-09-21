<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ContentController extends Controller
{
    public function careerGuide(): View
    {
        return view('content.career-guide');
    }

    public function pricing(): View
    {
        return view('content.pricing');
    }

    public function blog(): View
    {
        return view('content.blog', ['articles' => $this->articles()]);
    }

    public function article(string $slug): View
    {
        $article = collect($this->articles())->firstWhere('slug', $slug);
        abort_unless($article, 404);

        return view('content.article', compact('article'));
    }

    /** @return array<int, array<string, string>> */
    private function articles(): array
    {
        return [
            ['slug' => 'build-a-cv-that-gets-noticed', 'title' => 'How to Build a CV That Gets Noticed', 'category' => 'CV advice', 'date' => 'September 16, 2026', 'excerpt' => 'Make your experience clear, relevant, and easy for hiring teams to scan.', 'body' => 'Start with the role you want, then bring the most relevant experience to the top. Use a simple layout, specific achievements, and language that reflects the job description. Before sending it, check that your contact details, dates, and file name are professional.'],
            ['slug' => 'mistakes-jobseekers-should-avoid', 'title' => '7 Mistakes Jobseekers Should Avoid', 'category' => 'Job search', 'date' => 'September 12, 2026', 'excerpt' => 'Small changes in preparation can make every application stronger.', 'body' => 'Avoid sending the same CV everywhere, applying without reading the requirements, and leaving your online profile out of date. Keep a record of applications, prepare thoughtful questions, and follow up respectfully when appropriate. Consistency is more useful than rushing.'],
            ['slug' => 'prepare-for-a-job-interview', 'title' => 'How to Prepare for a Job Interview', 'category' => 'Interviews', 'date' => 'September 8, 2026', 'excerpt' => 'A practical preparation routine that helps you speak with confidence.', 'body' => 'Research the organisation, review the role, and prepare a few examples that show how you work. Practice explaining your achievements with context, action, and results. Plan the practical details in advance so you can focus on the conversation.'],
            ['slug' => 'grow-your-career-in-2026', 'title' => 'How to Grow Your Career in 2026', 'category' => 'Career growth', 'date' => 'September 4, 2026', 'excerpt' => 'Build momentum through focused learning, feedback, and meaningful work.', 'body' => 'Choose one or two skills that support the direction you want to take. Look for projects that let you demonstrate them, ask for feedback, and keep a record of your progress. Career growth is usually a series of deliberate, visible steps.'],
            ['slug' => 'skills-employers-are-looking-for', 'title' => 'Skills Employers Are Looking For', 'category' => 'Workplace skills', 'date' => 'August 30, 2026', 'excerpt' => 'Show both the practical capabilities and habits that help teams succeed.', 'body' => 'Employers value people who can do the core work and collaborate well while doing it. Highlight role-specific knowledge alongside communication, problem solving, reliability, and a willingness to learn. Use examples rather than broad claims.'],
            ['slug' => 'attract-better-candidates', 'title' => 'How Employers Can Attract Better Candidates', 'category' => 'Hiring', 'date' => 'August 26, 2026', 'excerpt' => 'A clear, respectful hiring experience encourages strong people to apply.', 'body' => 'Describe the role honestly, explain what success looks like, and keep candidates informed. A straightforward application process and timely communication reflect how your organisation works. The best candidates evaluate employers too.'],
            ['slug' => 'why-a-strong-employer-brand-matters', 'title' => 'Why a Strong Employer Brand Matters', 'category' => 'Employer brand', 'date' => 'August 22, 2026', 'excerpt' => 'Your reputation as a workplace begins long before an offer is made.', 'body' => 'An employer brand is the consistent story people hear from your team, candidates, and customers. Share real values, working practices, and growth opportunities. Align the recruitment experience with that story to build trust.'],
            ['slug' => 'write-a-better-job-description', 'title' => 'How to Write a Better Job Description', 'category' => 'Hiring', 'date' => 'August 18, 2026', 'excerpt' => 'Give candidates the information they need to decide whether the role fits.', 'body' => 'Use a clear title, explain the purpose of the role, and separate essential requirements from nice-to-haves. Describe the team, responsibilities, and application process in plain language. Specificity helps the right people recognise a good fit.'],
        ];
    }
}
