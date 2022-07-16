<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentaireRepository::class)
 */
class Commentaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $auteur;

    /**
     * @ORM\Column(type="text")
     */
    private $contenu;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="commentaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $entreprise;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(1, message= "Veuillez insérez une note entre 1 et 5.")
     * @Assert\LessThanOrEqual(5, message= "Veuillez insérez une note entre 1 et 5.")
     */
    private $note;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function getNoteToStars(): ?string
    {
        $rateResult = "";
        $count = 0;
        $maxRate = 5;
        if ($this->note >= 1 && $this->note <= 5){
            switch ($this->note) {
                case $this->note:
                    while ($count < $this->note) {
                        $rateResult .= "<i class='fa-solid fa-star'></i>";
                        $count++;
                    }
                    while ($this->note != $maxRate){
                        $rateResult .= "<i class='fa-solid fa-star unfilled-star'></i>";
                        $this->note++;
                    }
                    return $rateResult;
                    break;

                default:
                    return "La note n'est pas défini pour cette entreprise.";
                    break;
            }
        }else{
            return "La note inséré n'est pas entre 1 et 5.";
        }
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function __toString()
    {
        return $this->auteur. " ". $this->contenu. " ";
    }

    
}
