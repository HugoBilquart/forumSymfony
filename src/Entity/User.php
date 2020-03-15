<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birth_date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $biography;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $signature;

    /**
     * @ORM\Column(type="date")
     */
    private $registration_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_login;

    /**
     * @ORM\Column(type="integer")
     */
    private $is_muted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(?\DateTimeInterface $birth_date): self
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registration_date;
    }

    public function setRegistrationDate(\DateTimeInterface $registration_date): self
    {
        $this->registration_date = $registration_date;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->last_login;
    }

    public function setLastLogin(?\DateTimeInterface $last_login): self
    {
        $this->last_login = $last_login;

        return $this;
    }

    public function getIsMuted(): ?int
    {
        return $this->is_muted;
    }

    public function setIsMuted(int $is_muted): self
    {
        $this->is_muted = $is_muted;

        return $this;
    }

    public function roleStr(string $role) {
        switch ($role) {
            case 'ROLE_USER':
                return 'member';
                break;
            
            case 'ROLE_MODO':
                return 'moderator';
                break;

            case 'ROLE_ADMIN':
                return 'administrator';
                break;
        }
    }

    public function getLastLoginStr($last) {
        $now = new \DateTime();

        $difference = date_diff($last,$now);

        if($difference->format('%y') >= 1) {
            if($difference->format('%y') == 1) {
                $diff = $difference->format('%y')." year";
            }
            else {
                $diff = $difference->format('%y')." years";
            }
        }
        else if($difference->format('%i') < 1) {
            $diff = "Just now";
        }
        else if($difference->format('%h') < 1) {
            if($difference->format('%m') == 1) {
                $diff = $difference->format('%i')." minute";
            }
            else {
                $diff = $difference->format('%i')." minutes";
            }
        }
        else if($difference->format('%d') < 1) {
            if($difference->format('%h') == 1) {
                $diff = $difference->format('%h')." hour";
            }
            else {
                $diff = $difference->format('%h')." hours";
            }
        }
        else if($difference->format('%m') < 1) {
            if($difference->format('%d') == 1) {
                $diff = $difference->format('%d')." day";
            }
            else {
                $diff = $difference->format('%d')." days";
            }
        }
        else if($difference->format('%y') < 1) {
            if($difference->format('%m') == 1) {
                $diff = $difference->format('%m')." month";
            }
            else {
                $diff = $difference->format('%m')." months";
            }
        }
        return $diff;
    }

    public function createAvatarFile($username) {
        $file = 'img/users/default.png';
		$newfile = 'img/users/'.$username.'.png';
		if (!copy($file, $newfile)) {
    		echo "<p class='failed'>Failed to create new user avatar\n</p>";
		}
		else {
			return $newfile;
		}
    }

    public function age($birthday) {
        return date_diff($birthday,date_create(date('Y-m-d')))->format('%y');
    }
}
