<?php

namespace App\Service;

class BreadcrumbsService
{
    private array $items = [];

    public function add(string $label, ?string $route = null, array $params = []): self
    {
        $this->items[] = [
            'label' => $label,
            'route' => $route,
            'params' => $params,
        ];

        return $this;
    }

    public function addHome(): self
    {
        return $this->add('Home', 'app_index');
    }

    public function addApproval(): self
    {
        return $this->add('Approvals', 'approval_index');
    }

    public function addCategory(): self
    {
        return $this->add('Category', 'category_index');
    }

    public function addClimb(): self
    {
        return $this->add('Climb', 'climb_index');
    }

    public function addLeaderboard(): self
    {
        return $this->add('Leaderboard', 'app_leaderboard');
    }

    public function addRankMethod(): self
    {
        return $this->add('Rank Method', 'rank_method_index');
    }

    public function addSubcategory(): self
    {
        return $this->add('Subcategory', 'subcategory_index');
    }

    public function addClimber(): self
    {
        return $this->add('Climber', 'user_index');
    }

    public function all(): array
    {
        return $this->items;
    }

    public function reset(): void
    {
        $this->items = [];
    }
}
